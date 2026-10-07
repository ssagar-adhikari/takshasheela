<?php

namespace App\Http\Controllers;

use App\Models\WellnessCategory;
use App\Models\WellnessSubcategory;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WellnessSubcategoryController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->integer('category') ?: null;

        return view('admin.wellness.subcategories.index', [
            'subcategories' => WellnessSubcategory::with('category')->withCount('offerings')
                ->when($categoryId, fn ($query) => $query->where('wellness_category_id', $categoryId))
                ->orderBy('wellness_category_id')->orderBy('sort_order')->orderBy('name')->get(),
            'categories' => WellnessCategory::orderBy('sort_order')->orderBy('name')->get(),
            'categoryId' => $categoryId,
        ]);
    }

    public function create(Request $request)
    {
        $categoryId = WellnessCategory::whereKey($request->integer('category'))->value('id') ?: WellnessCategory::orderBy('sort_order')->value('id');

        return view('admin.wellness.subcategories.form', [
            'subcategory' => new WellnessSubcategory([
                'wellness_category_id' => $categoryId,
                'is_published' => true,
                'sort_order' => ((int) WellnessSubcategory::where('wellness_category_id', $categoryId)->max('sort_order')) + 10,
            ]),
            'categories' => WellnessCategory::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name'], $data['wellness_category_id']);
        $data['updated_by'] = $request->user()->id;
        $subcategory = WellnessSubcategory::create($data);

        return redirect()->route('admin.wellness-subcategories.edit', $subcategory)
            ->with('status', $subcategory->name.' subcategory created. You can now add offerings beneath it.');
    }

    public function edit(WellnessSubcategory $wellnessSubcategory)
    {
        return view('admin.wellness.subcategories.form', [
            'subcategory' => $wellnessSubcategory,
            'categories' => WellnessCategory::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, WellnessSubcategory $wellnessSubcategory)
    {
        $data = $this->validated($request);
        if ((int) $data['wellness_category_id'] !== $wellnessSubcategory->wellness_category_id) {
            if ($wellnessSubcategory->offerings()->exists()) {
                return back()->withInput()->withErrors([
                    'wellness_category_id' => 'Move or delete every offering before moving this subcategory to another category.',
                ]);
            }
            $data['slug'] = $this->uniqueSlug($data['name'], $data['wellness_category_id']);
        }
        $data['updated_by'] = $request->user()->id;
        $wellnessSubcategory->update($data);

        return back()->with('status', $data['name'].' subcategory saved.');
    }

    public function destroy(WellnessSubcategory $wellnessSubcategory)
    {
        if ($wellnessSubcategory->offerings()->exists()) {
            return back()->withErrors(['subcategory' => 'Delete or move every offering in this subcategory before deleting it.']);
        }

        $name = $wellnessSubcategory->name;
        $wellnessSubcategory->delete();

        return redirect()->route('admin.wellness-subcategories.index')->with('status', $name.' subcategory deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'wellness_category_id' => ['required', 'integer', 'exists:wellness_categories,id'],
            'name' => ['required', 'string', 'max:140'],
            'nav_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['sometimes', 'boolean'],
        ]);
        $validated['description'] = RichText::sanitize($validated['description'] ?? null);
        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    private function uniqueSlug(string $value, int $categoryId): string
    {
        $base = Str::slug($value) ?: 'wellness-subcategory';
        $slug = $base;
        $suffix = 2;

        while (WellnessSubcategory::where('wellness_category_id', $categoryId)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
