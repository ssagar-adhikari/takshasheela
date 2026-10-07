<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        return view('admin.testimonials.index', [
            'testimonials' => Testimonial::orderBy('sort_order')->orderBy('guest_name')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.testimonials.form', [
            'testimonial' => new Testimonial([
                'rating' => 5,
                'is_published' => true,
                'sort_order' => ((int) Testimonial::max('sort_order')) + 10,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $testimonial = Testimonial::create($this->validated($request) + ['updated_by' => $request->user()->id]);

        return redirect()->route('admin.testimonials.edit', $testimonial)
            ->with('status', $testimonial->guest_name.' testimonial created.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validated($request) + ['updated_by' => $request->user()->id]);

        return back()->with('status', $testimonial->guest_name.' testimonial saved.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $name = $testimonial->guest_name;
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('status', $name.' testimonial deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'quote' => ['required', 'string', 'max:5000'],
            'guest_name' => ['required', 'string', 'max:150'],
            'guest_location' => ['nullable', 'string', 'max:150'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['sometimes', 'boolean'],
        ]);
        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }
}
