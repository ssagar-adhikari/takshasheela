@extends('layouts.admin')
@php
    $editing = $product->exists;
    $ingredients = old('ingredients_text', implode("
", $product->ingredients ?? []));
@endphp
@section('title', $editing ? 'Edit '.$product->name : 'Add product')
@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">PRODUCT</p>
        <h1>{{ $editing ? 'Edit '.$product->name : 'Add product' }}</h1>
        <p class="muted">This content is shared by the product catalogue and detail page.</p>
    </div>
    <a class="button button-outline" href="{{ route('admin.products.index') }}">← All products</a>
</div>
<form method="post" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="stack about-editor">
    @csrf
    @if($editing) @method('PUT') @endif

    <section class="panel form-panel">
        <div><p class="eyebrow">LISTING PAGE</p><h2>Product information</h2><p class="muted">Used on catalogue cards, the homepage, and related-product cards.</p></div>
        <label>Name<input name="name" value="{{ old('name', $product->name) }}" maxlength="150" required><small>The website URL is generated automatically when you create the product.</small></label>
        <div class="form-columns">
            <label>Category<input name="category" value="{{ old('category', $product->category) }}" maxlength="150" required></label>
            <label>Size<input name="size" value="{{ old('size', $product->size) }}" maxlength="80" placeholder="200 ml" required></label>
        </div>
        <label>Price<input name="price" value="{{ old('price', $product->price) }}" maxlength="80" placeholder="NPR 1,850" required></label>
        <label>Short description<textarea name="short_description" rows="3" maxlength="1000" required>{{ old('short_description', $product->short_description) }}</textarea></label>
    </section>

    <section class="panel form-panel">
        <div><p class="eyebrow">IMAGE</p><h2>Product image</h2><p class="muted">Shown throughout the catalogue and on the product detail page.</p></div>
        <div class="image-picker" data-image-picker>
            <span class="image-picker-label">Product image</span>
            <div class="image-picker-body">
                <img class="image-picker-preview product-image-preview" data-image-preview @if($product->imageUrl()) src="{{ $product->imageUrl() }}" @else hidden @endif alt="Current product image">
                <div class="image-picker-controls">
                    <label>Choose image from device<input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-input @required(!$editing)><small>JPG, PNG, or WebP. Maximum 2 MB and 6000 × 6000 pixels.</small></label>
                    <p class="image-picker-feedback muted" data-image-feedback aria-live="polite"></p>
                </div>
            </div>
        </div>
        <label>Image description<input name="image_alt" value="{{ old('image_alt', $product->image_alt) }}" maxlength="255" required><small>Describe the image for visitors using screen readers.</small></label>
    </section>

    <section class="panel form-panel">
        <div><p class="eyebrow">DETAIL PAGE</p><h2>Product details</h2><p class="muted">Displayed after a visitor opens this product.</p></div>
        <label>Full description<textarea data-wysiwyg name="description" rows="6" maxlength="5000" required>{{ old('description', $product->description) }}</textarea></label>
        <label>Key ingredients<textarea name="ingredients_text" rows="6" maxlength="5000" required>{{ $ingredients }}</textarea><small>Enter one ingredient per line, up to 30.</small></label>
        <label>How to use<textarea data-wysiwyg name="usage" rows="5" maxlength="5000" required>{{ old('usage', $product->usage) }}</textarea></label>
    </section>

    <section class="panel form-panel">
        <div><p class="eyebrow">VISIBILITY</p><h2>Publishing and order</h2></div>
        <div class="form-columns">
            <label>Display order<input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0" max="9999" required><small>Lower numbers appear first.</small></label>
            <label class="checkbox accommodation-publish"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $product->is_published))> Published on the website</label>
        </div>
    </section>

    <div class="about-save-bar">
        <p class="muted">Saving updates the catalogue, homepage product section, and detail page. Draft products stay hidden.</p>
        <div class="about-save-actions">
            <a class="button button-outline" href="{{ route('admin.products.index') }}">Cancel</a>
            <button type="submit" class="button">{{ $editing ? 'Save product' : 'Create product' }} →</button>
        </div>
    </div>
</form>
@endsection
