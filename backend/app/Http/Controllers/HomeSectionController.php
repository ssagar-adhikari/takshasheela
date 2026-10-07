<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class HomeSectionController extends Controller
{
    public function edit()
    {
        return view('admin.home.edit', [
            'heroSection' => $this->section('hero'),
            'philosophySection' => $this->section('philosophy'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero' => ['required', 'array:eyebrow,title,description,scroll_note,poster_alt'],
            'hero.eyebrow' => ['nullable', 'string', 'max:150'],
            'hero.title' => ['required', 'string', 'max:255'],
            'hero.description' => ['nullable', 'string', 'max:1000'],
            'hero.scroll_note' => ['nullable', 'string', 'max:150'],
            'hero.poster_alt' => ['nullable', 'string', 'max:255'],
            'philosophy' => ['required', 'array:quote,attribution'],
            'philosophy.quote' => ['required', 'string', 'max:1000'],
            'philosophy.attribution' => ['nullable', 'string', 'max:255'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=6000,max_height=6000'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm', 'mimetypes:video/mp4,video/webm', 'max:81920'],
            'remove_poster' => ['sometimes', 'boolean'],
            'remove_video' => ['sometimes', 'boolean'],
        ]);

        $validated['hero']['description'] = RichText::sanitize($validated['hero']['description']);

        $newPaths = [];
        $obsoletePaths = [];

        try {
            DB::transaction(function () use ($request, $validated, &$newPaths, &$obsoletePaths) {
                $heroSection = HomeSection::where('slug', 'hero')->lockForUpdate()->firstOrFail();
                $philosophySection = HomeSection::where('slug', 'philosophy')->lockForUpdate()->firstOrFail();
                $hero = array_replace($heroSection->content, $validated['hero']);
                $oldPaths = array_filter([$hero['poster_path'] ?? null, $hero['video_path'] ?? null]);

                if ($request->boolean('remove_poster')) {
                    $hero['poster_path'] = null;
                }
                if ($request->boolean('remove_video')) {
                    $hero['video_path'] = null;
                }
                if ($request->hasFile('poster')) {
                    $hero['poster_path'] = $this->store($request->file('poster'), 'homepage/hero', $newPaths);
                }
                if ($request->hasFile('video')) {
                    $hero['video_path'] = $this->store($request->file('video'), 'homepage/hero', $newPaths);
                }

                $heroSection->update(['content' => $hero, 'updated_by' => $request->user()->id]);
                $philosophySection->update(['content' => $validated['philosophy'], 'updated_by' => $request->user()->id]);

                $currentPaths = array_filter([$hero['poster_path'] ?? null, $hero['video_path'] ?? null]);
                $obsoletePaths = array_filter(array_diff($oldPaths, $currentPaths), fn (string $path) => ! str_starts_with($path, 'assets/'));
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }

        Storage::disk('public')->delete(array_values($obsoletePaths));

        return back()->with('status', 'Homepage hero and philosophy sections saved.');
    }

    private function section(string $slug): HomeSection
    {
        return HomeSection::firstOrCreate(
            ['slug' => $slug],
            ['content' => config('home.sections.'.$slug)],
        );
    }

    private function store($file, string $directory, array &$newPaths): string
    {
        $path = $file->store($directory, 'public');
        if (! $path) {
            throw new RuntimeException('The media file could not be stored. Please try again.');
        }
        $newPaths[] = $path;

        return $path;
    }
}
