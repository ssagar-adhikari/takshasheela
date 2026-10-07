<?php

namespace App\Http\Controllers;

use App\Models\WellnessCategory;
use App\Models\WellnessOffering;
use App\Models\WellnessSubcategory;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class WellnessOfferingController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->integer('category') ?: null;
        $subcategoryId = $request->integer('subcategory') ?: null;
        $query = WellnessOffering::with(['category', 'subcategory'])
            ->orderBy('wellness_category_id')->orderBy('wellness_subcategory_id')->orderBy('sort_order')->orderBy('name');

        return view('admin.wellness.offerings.index', [
            'offerings' => $query
                ->when($categoryId, fn ($query) => $query->where('wellness_category_id', $categoryId))
                ->when($subcategoryId, fn ($query) => $query->where('wellness_subcategory_id', $subcategoryId))
                ->paginate(20)->withQueryString(),
            'categories' => WellnessCategory::orderBy('sort_order')->orderBy('name')->get(),
            'subcategories' => WellnessSubcategory::with('category')->orderBy('wellness_category_id')->orderBy('sort_order')->orderBy('name')->get(),
            'categoryId' => $categoryId,
            'subcategoryId' => $subcategoryId,
        ]);
    }

    public function create(Request $request)
    {
        $subcategory = WellnessSubcategory::with('category')->find($request->integer('subcategory'))
            ?? WellnessSubcategory::with('category')->orderBy('wellness_category_id')->orderBy('sort_order')->first();

        return view('admin.wellness.offerings.form', [
            'offering' => new WellnessOffering([
                'wellness_category_id' => $subcategory?->wellness_category_id,
                'wellness_subcategory_id' => $subcategory?->id,
                'is_published' => true,
                'sort_order' => ((int) WellnessOffering::where('wellness_subcategory_id', $subcategory?->id)->max('sort_order')) + 10,
                'highlights' => [],
                'includes' => [],
                'itinerary' => [],
            ]),
            'subcategories' => WellnessSubcategory::with('category')->orderBy('wellness_category_id')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        [$data, $image] = $this->validated($request);
        $subcategory = WellnessSubcategory::with('category')->findOrFail($data['wellness_subcategory_id']);
        $category = $subcategory->category;
        $data['wellness_category_id'] = $category->id;
        $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        $newPath = null;

        try {
            $newPath = $this->storeImage($image, $category->slug, $data['slug']);
            $data['image_path'] = $newPath;
            $data['updated_by'] = $request->user()->id;
            $offering = DB::transaction(fn () => WellnessOffering::create($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        return redirect()->route('admin.wellness-offerings.edit', $offering)
            ->with('status', $offering->name.' created. Its listing and detail pages are ready.');
    }

    public function edit(WellnessOffering $wellnessOffering)
    {
        return view('admin.wellness.offerings.form', [
            'offering' => $wellnessOffering,
            'subcategories' => WellnessSubcategory::with('category')->orderBy('wellness_category_id')->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, WellnessOffering $wellnessOffering)
    {
        [$data, $image] = $this->validated($request, $wellnessOffering);
        $subcategory = WellnessSubcategory::with('category')->findOrFail($data['wellness_subcategory_id']);
        $category = $subcategory->category;
        $data['wellness_category_id'] = $category->id;
        if ($category->id !== $wellnessOffering->wellness_category_id) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }
        $newPath = null;
        $oldPath = $wellnessOffering->image_path;

        try {
            if ($image) {
                $newPath = $this->storeImage($image, $category->slug, $data['slug'] ?? $wellnessOffering->slug);
                $data['image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;
            DB::transaction(fn () => WellnessOffering::whereKey($wellnessOffering->id)->lockForUpdate()->firstOrFail()->update($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath && $newPath !== $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', $data['name'].' saved. The public listing and detail page have been updated.');
    }

    public function destroy(WellnessOffering $wellnessOffering)
    {
        $imagePath = $wellnessOffering->image_path;
        $name = $wellnessOffering->name;
        $wellnessOffering->delete();
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.wellness-offerings.index')->with('status', $name.' deleted.');
    }

    private function validated(Request $request, ?WellnessOffering $offering = null): array
    {
        $validated = $request->validate([
            'wellness_subcategory_id' => ['required', 'integer', 'exists:wellness_subcategories,id'],
            'name' => ['required', 'string', 'max:180'],
            'eyebrow' => ['required', 'string', 'max:150'],
            'duration' => ['nullable', 'string', 'max:100'],
            'image_alt' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:1500'],
            'description' => ['required', 'string', 'max:8000'],
            'highlights_text' => ['required', 'string', 'max:4000'],
            'includes_text' => ['required', 'string', 'max:5000'],
            'itinerary_text' => ['nullable', 'string', 'max:12000'],
            'ideal_for' => ['required', 'string', 'max:5000'],
            'secondary_title' => ['required', 'string', 'max:150'],
            'secondary_content' => ['required', 'string', 'max:5000'],
            'notice' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['sometimes', 'boolean'],
            'image' => [
                $offering ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);

        foreach (['description', 'ideal_for', 'secondary_content', 'notice'] as $field) {
            $validated[$field] = RichText::sanitize($validated[$field] ?? null);
        }
        $validated['highlights'] = $this->lines($validated['highlights_text'], 'highlights_text', 20);
        $validated['includes'] = $this->lines($validated['includes_text'], 'includes_text', 30);
        $validated['itinerary'] = $this->itinerary($validated['itinerary_text'] ?? '');
        $validated['is_published'] = $request->boolean('is_published');
        $image = $request->file('image');
        unset($validated['highlights_text'], $validated['includes_text'], $validated['itinerary_text'], $validated['image']);

        return [$validated, $image];
    }

    private function lines(string $value, string $field, int $maximum): array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', $value)), fn (string $line) => $line !== ''));
        if (count($lines) > $maximum) {
            throw ValidationException::withMessages([$field => 'Enter no more than '.$maximum.' items.']);
        }
        if (collect($lines)->contains(fn (string $line) => mb_strlen($line) > 255)) {
            throw ValidationException::withMessages([$field => 'Each item must be 255 characters or fewer.']);
        }

        return $lines;
    }

    private function itinerary(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        $rows = array_values(array_filter(array_map('trim', preg_split('/\R/', $value)), fn (string $line) => $line !== ''));
        if (count($rows) > 30) {
            throw ValidationException::withMessages(['itinerary_text' => 'Enter no more than 30 itinerary steps.']);
        }

        return array_map(function (string $row) {
            $parts = array_map('trim', explode('|', $row, 3));
            if (count($parts) !== 3 || in_array('', $parts, true)) {
                throw ValidationException::withMessages(['itinerary_text' => 'Use one line per step in this format: Time | Title | Description.']);
            }
            if (mb_strlen($parts[0]) > 100 || mb_strlen($parts[1]) > 180 || mb_strlen($parts[2]) > 1000) {
                throw ValidationException::withMessages(['itinerary_text' => 'An itinerary step is too long. Keep time under 100, title under 180, and description under 1000 characters.']);
            }

            return ['time' => $parts[0], 'title' => $parts[1], 'description' => $parts[2]];
        }, $rows);
    }

    private function uniqueSlug(string $value, int $categoryId): string
    {
        $base = Str::slug($value) ?: 'wellness-program';
        $slug = $base;
        $suffix = 2;

        while (WellnessOffering::where('wellness_category_id', $categoryId)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function storeImage($image, string $categorySlug, string $slug): string
    {
        $path = $image->store('wellness/offerings/'.$categorySlug.'/'.$slug, 'public');
        if (! $path) {
            throw new RuntimeException('The program image could not be stored. Please try again.');
        }

        return $path;
    }
}
