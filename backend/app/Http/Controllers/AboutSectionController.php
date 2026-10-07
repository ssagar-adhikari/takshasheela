<?php

namespace App\Http\Controllers;

use App\Models\AboutSection;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class AboutSectionController extends Controller
{
    public function edit(string $section)
    {
        $record = AboutSection::where('slug', $section)->firstOrFail();

        return view('admin.about.edit', [
            'section' => $record,
            'menu' => config('about.sections.'.$section),
        ]);
    }

    public function update(Request $request, string $section)
    {
        $record = AboutSection::where('slug', $section)->firstOrFail();
        $keys = array_keys($record->content['blocks']);
        $isTeam = $record->slug === 'our-team';
        $fixedKeys = array_keys(array_filter(
            $record->content['blocks'],
            fn (array $block) => ! $isTeam || $block['kind'] !== 'person'
        ));
        $fields = [
            'meta_title' => 100, 'meta_description' => 300,
            'hero_eyebrow' => 150, 'hero_title' => 255, 'hero_description' => 1000,
            'hero_image_alt' => 255, 'cta_eyebrow' => 150, 'cta_title' => 500, 'cta_body' => 1000,
        ];
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'];
        $rules = [
            'content' => ['required', 'array:'.implode(',', [...array_keys($fields), 'blocks'])],
            'content.blocks' => $isTeam
                ? ['required', 'array', 'max:52']
                : ['required', 'array:'.implode(',', $keys)],
            'hero_image' => $imageRules,
            'remove_hero_image' => ['sometimes', 'boolean'],
            'block_images' => $isTeam
                ? ['sometimes', 'array', 'max:52']
                : ['sometimes', 'array:'.implode(',', $keys)],
            'remove_images' => $isTeam
                ? ['sometimes', 'array', 'max:52']
                : ['sometimes', 'array:'.implode(',', $keys)],
        ];
        foreach ($fields as $key => $max) {
            $rules['content.'.$key] = [in_array($key, ['meta_title', 'hero_title', 'cta_title']) ? 'required' : 'nullable', 'string', 'max:'.$max];
        }
        foreach ($isTeam ? $fixedKeys : $keys as $key) {
            $rules['content.blocks.'.$key] = ['required', 'array:eyebrow,title,body,image_alt'];
            foreach (['eyebrow' => 150, 'title' => 255, 'body' => 20000, 'image_alt' => 255] as $field => $max) {
                $rules['content.blocks.'.$key.'.'.$field] = [$field === 'title' ? 'required' : 'nullable', 'string', 'max:'.$max];
            }
            $rules['block_images.'.$key] = $imageRules;
            $rules['remove_images.'.$key] = ['sometimes', 'boolean'];
        }
        if ($isTeam) {
            $rules['content.blocks.*'] = ['required', 'array:eyebrow,title,body,image_alt'];
            foreach (['eyebrow' => 150, 'title' => 255, 'body' => 20000, 'image_alt' => 255] as $field => $max) {
                $rules['content.blocks.*.'.$field] = [$field === 'title' ? 'required' : 'nullable', 'string', 'max:'.$max];
            }
            $rules['block_images.*'] = $imageRules;
            $rules['remove_images.*'] = ['sometimes', 'boolean'];
        }

        $validator = Validator::make($request->all(), $rules);
        if ($isTeam) {
            $validator->after(function ($validator) use ($request, $record, $fixedKeys) {
                $blocks = $request->input('content.blocks', []);
                $submittedKeys = array_keys(is_array($blocks) ? $blocks : []);
                $imageKeys = array_keys($request->file('block_images', []));
                $removeKeys = array_keys($request->input('remove_images', []));
                $knownPeople = array_keys(array_filter(
                    $record->content['blocks'],
                    fn (array $block) => $block['kind'] === 'person'
                ));

                foreach (array_unique([...$submittedKeys, ...$imageKeys, ...$removeKeys]) as $key) {
                    $isKnown = in_array($key, [...$fixedKeys, ...$knownPeople], true);
                    $isNewMember = preg_match('/^member-[a-z0-9-]{8,80}$/', (string) $key) === 1;
                    if (! $isKnown && ! $isNewMember) {
                        $validator->errors()->add('content.blocks', 'The team member list contains an invalid entry.');
                    }
                    if (($isNewMember || in_array($key, $knownPeople, true)) && ! in_array($key, $submittedKeys, true)) {
                        $validator->errors()->add('block_images', 'An image was submitted for a team member that is not being saved.');
                    }
                }

                if (count(array_diff($submittedKeys, $fixedKeys)) > 50) {
                    $validator->errors()->add('content.blocks', 'You may add up to 50 team members.');
                }
            });
        }
        $validated = $validator->validate();
        $validated['content']['hero_description'] = RichText::sanitize($validated['content']['hero_description']);
        $validated['content']['cta_body'] = RichText::sanitize($validated['content']['cta_body']);
        foreach ($validated['content']['blocks'] as &$block) {
            $block['body'] = RichText::sanitize($block['body']);
        }
        unset($block);
        $uploads = array_filter([$request->file('hero_image'), ...array_values($request->file('block_images', []))]);
        if (array_sum(array_map(fn ($file) => $file->getSize(), $uploads)) > 6 * 1024 * 1024) {
            throw ValidationException::withMessages(['images' => 'Upload up to 6 MB of images per save. Save larger batches separately.']);
        }

        $newPaths = [];
        $obsoletePaths = [];
        try {
            DB::transaction(function () use ($record, $request, $validated, $isTeam, &$newPaths, &$obsoletePaths) {
                $locked = AboutSection::whereKey($record->id)->lockForUpdate()->firstOrFail();
                $content = $locked->content;
                $oldPaths = $this->imagePaths($content);
                foreach ($validated['content'] as $key => $value) {
                    if ($key !== 'blocks') {
                        $content[$key] = $value;
                    }
                }
                if ($request->boolean('remove_hero_image')) {
                    $content['hero_image'] = null;
                }
                if ($request->hasFile('hero_image')) {
                    $content['hero_image'] = $this->storeImage($request->file('hero_image'), $locked->slug, $newPaths);
                }
                if ($isTeam) {
                    $content['blocks'] = $this->teamBlocks($content['blocks'], $validated['content']['blocks'], $request, $locked->slug, $newPaths);
                } else {
                    foreach ($content['blocks'] as $key => &$block) {
                        $block = $this->updateBlock($block, $validated['content']['blocks'][$key], $key, $request, $locked->slug, $newPaths);
                    }
                    unset($block);
                }
                $locked->update(['content' => $content, 'updated_by' => $request->user()->id]);
                $obsoletePaths = array_diff($oldPaths, $this->imagePaths($content));
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }
        Storage::disk('public')->delete(array_values($obsoletePaths));

        return back()->with('status', config('about.sections.'.$section.'.label').' saved. Your website has been updated.');
    }

    private function storeImage($file, string $slug, array &$newPaths): string
    {
        $path = $file->store('about-us/'.$slug, 'public');
        if (! $path) {
            throw new RuntimeException('The image could not be stored. Please try again.');
        }
        $newPaths[] = $path;

        return $path;
    }

    private function imagePaths(array $content): array
    {
        return array_values(array_unique(array_filter([
            $content['hero_image'] ?? null,
            ...array_column($content['blocks'], 'image'),
        ])));
    }

    private function teamBlocks(array $currentBlocks, array $submittedBlocks, Request $request, string $slug, array &$newPaths): array
    {
        $blocks = [];

        foreach ($currentBlocks as $key => $block) {
            if ($block['kind'] !== 'person') {
                $blocks[$key] = $this->updateBlock($block, $submittedBlocks[$key], $key, $request, $slug, $newPaths);
            }
        }

        foreach ($submittedBlocks as $key => $values) {
            if (isset($currentBlocks[$key]) && $currentBlocks[$key]['kind'] !== 'person') {
                continue;
            }
            $block = $currentBlocks[$key] ?? [
                'kind' => 'person',
                'eyebrow' => '',
                'title' => '',
                'body' => '',
                'image' => null,
                'image_alt' => '',
            ];
            $blocks[$key] = $this->updateBlock($block, $values, $key, $request, $slug, $newPaths);
        }

        return $blocks;
    }

    private function updateBlock(array $block, array $values, string $key, Request $request, string $slug, array &$newPaths): array
    {
        $block = array_replace($block, $values);
        if ($request->boolean('remove_images.'.$key)) {
            $block['image'] = null;
        }
        if ($request->hasFile('block_images.'.$key)) {
            $block['image'] = $this->storeImage($request->file('block_images.'.$key), $slug, $newPaths);
        }

        return $block;
    }
}
