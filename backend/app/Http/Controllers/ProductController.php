<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::orderBy('sort_order')->orderBy('name')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product([
                'is_published' => true,
                'sort_order' => ((int) Product::max('sort_order')) + 10,
                'ingredients' => [],
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
            $product = DB::transaction(fn () => Product::create($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('status', $product->name.' created. It is ready for the product listing and detail page.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        [$data, $image] = $this->validated($request, $product);
        $newPath = null;
        $oldPath = $product->image_path;

        try {
            if ($image) {
                $newPath = $this->storeImage($image, $product->slug);
                $data['image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;

            DB::transaction(function () use ($product, $data) {
                Product::whereKey($product->id)->lockForUpdate()->firstOrFail()->update($data);
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

        return back()->with('status', $data['name'].' saved. The product listing and detail page have been updated.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $imagePath = $product->image_path;

        DB::transaction(function () use ($product) {
            Product::whereKey($product->id)->lockForUpdate()->firstOrFail()->delete();
        });

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.products.index')->with('status', $name.' deleted.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:150'],
            'size' => ['required', 'string', 'max:80'],
            'price' => ['required', 'string', 'max:80'],
            'image_alt' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:1000'],
            'description' => ['required', 'string', 'max:5000'],
            'ingredients_text' => ['required', 'string', 'max:5000'],
            'usage' => ['required', 'string', 'max:5000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['sometimes', 'boolean'],
            'image' => [
                $product ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);

        foreach (['description', 'usage'] as $field) {
            $validated[$field] = RichText::sanitize($validated[$field]);
        }
        $validated['ingredients'] = $this->lines($validated['ingredients_text']);
        $validated['is_published'] = $request->boolean('is_published');
        $image = $request->file('image');
        unset($validated['ingredients_text'], $validated['image']);

        return [$validated, $image];
    }

    private function lines(string $value): array
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/', $value)), fn (string $line) => $line !== ''));

        if (count($lines) > 30) {
            throw ValidationException::withMessages(['ingredients_text' => 'Enter no more than 30 ingredients.']);
        }
        if (collect($lines)->contains(fn (string $line) => mb_strlen($line) > 255)) {
            throw ValidationException::withMessages(['ingredients_text' => 'Each ingredient must be 255 characters or fewer.']);
        }

        return $lines;
    }

    private function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'product';
        $slug = $base;
        $suffix = 2;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function storeImage($image, string $slug): string
    {
        $path = $image->store('products/'.$slug, 'public');

        if (! $path) {
            throw new RuntimeException('The product image could not be stored. Please try again.');
        }

        return $path;
    }
}
