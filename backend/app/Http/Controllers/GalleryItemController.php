<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class GalleryItemController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index', [
            'items' => GalleryItem::orderBy('sort_order')->paginate(18),
        ]);
    }

    public function create()
    {
        return view('admin.gallery.form', [
            'item' => new GalleryItem([
                'is_published' => true,
                'sort_order' => ((int) GalleryItem::max('sort_order')) + 10,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        [$data, $image] = $this->validated($request);
        $newPath = null;

        try {
            $newPath = $this->storeImage($image);
            $data['image_path'] = $newPath;
            $data['updated_by'] = $request->user()->id;
            $item = DB::transaction(fn () => GalleryItem::create($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        return redirect()->route('admin.gallery.edit', $item)->with('status', 'Gallery image added.');
    }

    public function edit(GalleryItem $gallery)
    {
        return view('admin.gallery.form', ['item' => $gallery]);
    }

    public function update(Request $request, GalleryItem $gallery)
    {
        [$data, $image] = $this->validated($request, $gallery);
        $newPath = null;
        $oldPath = $gallery->image_path;

        try {
            if ($image) {
                $newPath = $this->storeImage($image);
                $data['image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;
            DB::transaction(fn () => GalleryItem::whereKey($gallery->id)->lockForUpdate()->firstOrFail()->update($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', 'Gallery image saved.');
    }

    public function destroy(GalleryItem $gallery)
    {
        $imagePath = $gallery->image_path;
        $gallery->delete();
        Storage::disk('public')->delete($imagePath);

        return redirect()->route('admin.gallery.index')->with('status', 'Gallery image deleted.');
    }

    private function validated(Request $request, ?GalleryItem $item = null): array
    {
        $validated = $request->validate([
            'image_alt' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['sometimes', 'boolean'],
            'image' => [
                $item ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $image = $request->file('image');
        unset($validated['image']);

        return [$validated, $image];
    }

    private function storeImage($image): string
    {
        $path = $image->store('gallery', 'public');

        if (! $path) {
            throw new RuntimeException('The gallery image could not be stored. Please try again.');
        }

        return $path;
    }
}
