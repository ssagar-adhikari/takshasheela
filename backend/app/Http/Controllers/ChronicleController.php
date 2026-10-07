<?php

namespace App\Http\Controllers;

use App\Models\ChronicleArticle;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class ChronicleController extends Controller
{
    public function index(Request $request)
    {
        $type = in_array($request->query('type'), ['blog', 'news'], true) ? $request->query('type') : null;
        $query = ChronicleArticle::orderBy('type')->orderBy('sort_order')->orderBy('title');

        return view('admin.chronicles.index', [
            'articles' => $query->when($type, fn ($query) => $query->where('type', $type))->paginate(15)->withQueryString(),
            'type' => $type,
        ]);
    }

    public function create(Request $request)
    {
        $type = in_array($request->query('type'), ['blog', 'news'], true) ? $request->query('type') : 'blog';

        return view('admin.chronicles.form', [
            'article' => new ChronicleArticle([
                'type' => $type,
                'is_published' => true,
                'is_featured' => false,
                'sort_order' => ((int) ChronicleArticle::where('type', $type)->max('sort_order')) + 10,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        [$data, $image] = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $newPath = null;

        try {
            if ($image) {
                $newPath = $this->storeImage($image, $data['slug']);
                $data['image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;

            $article = DB::transaction(function () use ($data) {
                if ($data['is_featured']) {
                    ChronicleArticle::where('type', $data['type'])->update(['is_featured' => false]);
                }

                return ChronicleArticle::create($data);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        return redirect()->route('admin.chronicles.edit', $article)
            ->with('status', $article->title.' created.');
    }

    public function edit(ChronicleArticle $chronicle)
    {
        return view('admin.chronicles.form', ['article' => $chronicle]);
    }

    public function update(Request $request, ChronicleArticle $chronicle)
    {
        [$data, $image] = $this->validated($request, $chronicle);
        $newPath = null;
        $oldPath = $chronicle->image_path;

        try {
            if ($image) {
                $newPath = $this->storeImage($image, $chronicle->slug);
                $data['image_path'] = $newPath;
            }
            $data['updated_by'] = $request->user()->id;

            DB::transaction(function () use ($chronicle, $data) {
                if ($data['is_featured']) {
                    ChronicleArticle::where('type', $data['type'])->whereKeyNot($chronicle->id)->update(['is_featured' => false]);
                }
                ChronicleArticle::whereKey($chronicle->id)->lockForUpdate()->firstOrFail()->update($data);
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

        return back()->with('status', $data['title'].' saved. Chronicle listings and the detail page have been updated.');
    }

    public function destroy(ChronicleArticle $chronicle)
    {
        $title = $chronicle->title;
        $imagePath = $chronicle->image_path;

        DB::transaction(function () use ($chronicle) {
            ChronicleArticle::whereKey($chronicle->id)->lockForUpdate()->firstOrFail()->delete();
        });

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.chronicles.index')->with('status', $title.' deleted.');
    }

    private function validated(Request $request, ?ChronicleArticle $article = null): array
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['blog', 'news'])],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:150'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'body' => ['required', 'string', 'max:30000'],
            'image_alt' => ['required', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'image' => [
                $article ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);

        $validated['body'] = RichText::sanitize($validated['body']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');
        $image = $request->file('image');
        unset($validated['image']);

        return [$validated, $image];
    }

    private function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'article';
        $slug = $base;
        $suffix = 2;

        while (ChronicleArticle::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function storeImage($image, string $slug): string
    {
        $path = $image->store('chronicles/'.$slug, 'public');

        if (! $path) {
            throw new RuntimeException('The Chronicle image could not be stored. Please try again.');
        }

        return $path;
    }
}
