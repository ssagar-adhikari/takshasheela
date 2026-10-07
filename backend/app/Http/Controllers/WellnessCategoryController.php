<?php

namespace App\Http\Controllers;

use App\Models\WellnessCategory;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class WellnessCategoryController extends Controller
{
    public function index()
    {
        return view('admin.wellness.categories.index', [
            'categories' => WellnessCategory::withCount(['subcategories', 'offerings'])->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.wellness.categories.form', [
            'category' => new WellnessCategory([
                'is_published' => true,
                'sort_order' => ((int) WellnessCategory::max('sort_order')) + 10,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        [$data, $image] = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $newPath = null;

        try {
            $newPath = $this->storeImage($image, $data['slug']);
            $data['hero_image_path'] = $newPath;
            $data['updated_by'] = $request->user()->id;
            $category = DB::transaction(fn () => WellnessCategory::create($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        return redirect()->route('admin.wellness-categories.edit', $category)
            ->with('status', $category->name.' category created. You can now add subcategories beneath it.');
    }

    public function edit(WellnessCategory $wellnessCategory)
    {
        return view('admin.wellness.categories.form', ['category' => $wellnessCategory]);
    }

    public function update(Request $request, WellnessCategory $wellnessCategory)
    {
        [$data, $image] = $this->validated($request, $wellnessCategory);
        $newPath = null;
        $oldPath = $wellnessCategory->hero_image_path;

        try {
            if ($image) {
                $newPath = $this->storeImage($image, $wellnessCategory->slug);
                $data['hero_image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;
            DB::transaction(fn () => WellnessCategory::whereKey($wellnessCategory->id)->lockForUpdate()->firstOrFail()->update($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath && $newPath !== $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', $data['name'].' category saved.');
    }

    public function destroy(WellnessCategory $wellnessCategory)
    {
        if ($wellnessCategory->subcategories()->exists()) {
            return back()->withErrors(['category' => 'Delete or move every subcategory in this category before deleting the category.']);
        }

        if ($wellnessCategory->offerings()->exists()) {
            return back()->withErrors(['category' => 'Delete or move every offering in this category before deleting the category.']);
        }

        $imagePath = $wellnessCategory->hero_image_path;
        $name = $wellnessCategory->name;
        $wellnessCategory->delete();
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.wellness-categories.index')->with('status', $name.' category deleted.');
    }

    private function validated(Request $request, ?WellnessCategory $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'nav_description' => ['required', 'string', 'max:255'],
            'hero_eyebrow' => ['required', 'string', 'max:150'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_description' => ['required', 'string', 'max:2000'],
            'hero_image_alt' => ['required', 'string', 'max:255'],
            'section_eyebrow' => ['required', 'string', 'max:150'],
            'section_title' => ['required', 'string', 'max:255'],
            'information_eyebrow' => ['nullable', 'string', 'max:150'],
            'information_title' => ['nullable', 'string', 'max:255'],
            'information_body' => ['nullable', 'string', 'max:4000'],
            'cta_eyebrow' => ['required', 'string', 'max:150'],
            'cta_title' => ['required', 'string', 'max:255'],
            'cta_body' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['sometimes', 'boolean'],
            'image' => [
                $category ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);

        foreach (['hero_description', 'information_body', 'cta_body'] as $field) {
            $validated[$field] = RichText::sanitize($validated[$field] ?? null);
        }
        $validated['is_published'] = $request->boolean('is_published');
        $image = $request->file('image');
        unset($validated['image']);

        return [$validated, $image];
    }

    private function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'wellness-category';
        $slug = $base;
        $suffix = 2;

        while (WellnessCategory::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function storeImage($image, string $slug): string
    {
        $path = $image->store('wellness/categories/'.$slug, 'public');
        if (! $path) {
            throw new RuntimeException('The category hero image could not be stored. Please try again.');
        }

        return $path;
    }
}
