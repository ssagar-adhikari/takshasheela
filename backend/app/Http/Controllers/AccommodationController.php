<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class AccommodationController extends Controller
{
    public function index()
    {
        return view('admin.accommodations.index', [
            'accommodations' => Accommodation::orderBy('sort_order')->orderBy('name')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.accommodations.form', [
            'accommodation' => new Accommodation([
                'is_published' => true,
                'sort_order' => ((int) Accommodation::max('sort_order')) + 10,
                'highlights' => [],
                'features' => [],
            ]),
        ]);
    }

    public function store(Request $request)
    {
        [$data, $image] = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $newPath = null;

        try {
            if ($image) {
                $newPath = $this->storeImage($image, $data['slug']);
                $data['image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;
            $accommodation = DB::transaction(fn () => Accommodation::create($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        return redirect()->route('admin.accommodations.edit', $accommodation)
            ->with('status', $accommodation->name.' created. It is ready for the accommodation listing and detail page.');
    }

    public function edit(Accommodation $accommodation)
    {
        return view('admin.accommodations.form', compact('accommodation'));
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        [$data, $image] = $this->validated($request, $accommodation);
        $newPath = null;
        $oldPath = $accommodation->image_path;

        try {
            if ($image) {
                $newPath = $this->storeImage($image, $accommodation->slug);
                $data['image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;

            DB::transaction(function () use ($accommodation, $data) {
                Accommodation::whereKey($accommodation->id)->lockForUpdate()->firstOrFail()->update($data);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', $data['name'].' saved. The accommodation listing and detail page have been updated.');
    }

    public function destroy(Accommodation $accommodation)
    {
        $name = $accommodation->name;
        $imagePath = $accommodation->image_path;

        DB::transaction(function () use ($accommodation) {
            Accommodation::whereKey($accommodation->id)->lockForUpdate()->firstOrFail()->delete();
        });

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.accommodations.index')->with('status', $name.' deleted.');
    }

    private function validated(Request $request, ?Accommodation $accommodation = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:150'],
            'heading' => ['required', 'string', 'max:255'],
            'image_alt' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:1000'],
            'description' => ['required', 'string', 'max:5000'],
            'highlights_text' => ['required', 'string', 'max:3000'],
            'features_text' => ['required', 'string', 'max:5000'],
            'ideal_for' => ['required', 'string', 'max:3000'],
            'stay_note' => ['required', 'string', 'max:3000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['sometimes', 'boolean'],
            'image' => [
                $accommodation ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);

        foreach (['description', 'ideal_for', 'stay_note'] as $field) {
            $validated[$field] = RichText::sanitize($validated[$field]);
        }
        $validated['highlights'] = $this->lines($validated['highlights_text'], 'highlights_text');
        $validated['features'] = $this->lines($validated['features_text'], 'features_text');
        $validated['is_published'] = $request->boolean('is_published');
        $image = $request->file('image');
        unset($validated['highlights_text'], $validated['features_text'], $validated['image']);

        return [$validated, $image];
    }

    private function lines(string $value, string $field): array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', $value)), fn (string $line) => $line !== ''));

        if (count($lines) > 20) {
            throw ValidationException::withMessages([$field => 'Enter no more than 20 items.']);
        }
        if (collect($lines)->contains(fn (string $line) => mb_strlen($line) > 255)) {
            throw ValidationException::withMessages([$field => 'Each item must be 255 characters or fewer.']);
        }

        return $lines;
    }

    private function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'accommodation';
        $slug = $base;
        $suffix = 2;

        while (Accommodation::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function storeImage($image, string $slug): string
    {
        $path = $image->store('accommodations/'.$slug, 'public');

        if (! $path) {
            throw new RuntimeException('The accommodation image could not be stored. Please try again.');
        }

        return $path;
    }
}
