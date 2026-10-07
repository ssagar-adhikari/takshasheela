@extends('layouts.admin')
@php($editing = $article->exists)
@section('title', $editing ? 'Edit '.$article->title : 'Add Chronicle article')
@section('content')
<div class="page-heading">
    <div><p class="eyebrow">CHRONICLES</p><h1>{{ $editing ? 'Edit article' : 'Add article' }}</h1><p class="muted">Create a Blog or News listing card and its full detail page.</p></div>
    <a class="button button-outline" href="{{ route('admin.chronicles.index') }}">← Blogs &amp; News</a>
</div>
<form method="post" action="{{ $editing ? route('admin.chronicles.update', $article) : route('admin.chronicles.store') }}" enctype="multipart/form-data" class="stack about-editor">
    @csrf @if($editing) @method('PUT') @endif
    <section class="panel form-panel">
        <div><p class="eyebrow">LISTING</p><h2>Article introduction</h2></div>
        <div class="form-columns">
            <label>Chronicle section<select name="type" required><option value="blog" @selected(old('type', $article->type) === 'blog')>Blog</option><option value="news" @selected(old('type', $article->type) === 'news')>News &amp; Events</option></select></label>
            <label>Category / eyebrow<input name="category" value="{{ old('category', $article->category) }}" maxlength="150" required></label>
        </div>
        <label>Title<input name="title" value="{{ old('title', $article->title) }}" maxlength="255" required><small>The website URL is generated automatically when you create the article.</small></label>
        <label>Excerpt<textarea name="excerpt" rows="3" maxlength="1000" required>{{ old('excerpt', $article->excerpt) }}</textarea><small>Shown on listing and homepage cards.</small></label>
    </section>
    <section class="panel form-panel">
        <div><p class="eyebrow">IMAGE</p><h2>Featured image</h2></div>
        <div class="image-picker" data-image-picker>
            <span class="image-picker-label">Article image</span>
            <div class="image-picker-body">
                <img class="image-picker-preview" data-image-preview @if($article->imageUrl()) src="{{ $article->imageUrl() }}" @else hidden @endif alt="Current article image">
                <div class="image-picker-controls"><label>Choose image from device<input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-input @required(!$editing)><small>JPG, PNG, or WebP. Maximum 2 MB and 6000 × 6000 pixels.</small></label><p class="image-picker-feedback muted" data-image-feedback aria-live="polite"></p></div>
            </div>
        </div>
        <label>Image description<input name="image_alt" value="{{ old('image_alt', $article->image_alt) }}" maxlength="255" required></label>
    </section>
    <section class="panel form-panel">
        <div><p class="eyebrow">DETAIL PAGE</p><h2>Article body</h2></div>
        <label>Content<textarea data-wysiwyg name="body" rows="14" maxlength="30000" required>{{ old('body', $article->body) }}</textarea><small>Use the toolbar to format headings, emphasis, lists, and quotations.</small></label>
    </section>
    <section class="panel form-panel">
        <div><p class="eyebrow">VISIBILITY</p><h2>Publishing and order</h2></div>
        <div class="form-columns">
            <label>Publish date<input type="datetime-local" name="published_at" value="{{ old('published_at', $article->published_at?->format('Y-m-dTH:i')) }}"></label>
            <label>Display order<input type="number" name="sort_order" value="{{ old('sort_order', $article->sort_order) }}" min="0" max="9999" required><small>Lower numbers appear first.</small></label>
        </div>
        <div class="form-columns">
            <label class="checkbox accommodation-publish"><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $article->is_featured))> Featured article for this section</label>
            <label class="checkbox accommodation-publish"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published))> Published on the website</label>
        </div>
    </section>
    <div class="about-save-bar"><p class="muted">Published articles appear on their section, homepage, and detail page.</p><div class="about-save-actions"><a class="button button-outline" href="{{ route('admin.chronicles.index') }}">Cancel</a><button class="button" type="submit">{{ $editing ? 'Save article' : 'Create article' }} →</button></div></div>
</form>
@endsection
