<?php

namespace App\Http\Controllers;

use App\Models\WellnessCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WellnessController extends Controller
{
    public function index(Request $request): View
    {
        $category = WellnessCategory::published()->where('slug', $request->route('categorySlug'))->firstOrFail();

        return view('site.wellness.index', [
            'category' => $category,
            'subcategories' => $category->subcategories()->published()
                ->with(['offerings' => fn ($query) => $query->published()])->get(),
        ]);
    }

    public function show(Request $request): View
    {
        $category = WellnessCategory::published()->where('slug', $request->route('categorySlug'))->firstOrFail();
        $offering = $category->offerings()->published()->where('slug', $request->route('offeringSlug'))->firstOrFail();

        return view('site.wellness.show', [
            'category' => $category,
            'offering' => $offering,
            'related' => $category->offerings()->published()
                ->where('wellness_subcategory_id', $offering->wellness_subcategory_id)
                ->whereKeyNot($offering->id)->get(),
        ]);
    }
}
