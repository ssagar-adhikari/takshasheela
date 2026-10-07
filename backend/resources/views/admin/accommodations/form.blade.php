@extends('layouts.admin')
@php
    $editing = $accommodation->exists;
    $highlights = old('highlights_text', implode("
", $accommodation->highlights ?? []));
    $features = old('features_text', implode("
", $accommodation->features ?? []));
@endphp
@section('title', $editing ? 'Edit '.$accommodation->name : 'Add accommodation')
@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">ACCOMMODATION</p>
        <h1>{{ $editing ? 'Edit '.$accommodation->name : 'Add accommodation' }}</h1>
        <p class="muted">This content is shared by the accommodation listing and detail page.</p>
    </div>
    <a class="button button-outline" href="{{ route('admin.accommodations.index') }}">← All accommodations</a>
</div>
<form method="post" action="{{ $editing ? route('admin.accommodations.update', $accommodation) : route('admin.accommodations.store') }}" enctype="multipart/form-data" class="stack about-editor">
    @csrf
    @if($editing) @method('PUT') @endif

    <section class="panel form-panel">
        <div><p class="eyebrow">LISTING PAGE</p><h2>Room introduction</h2><p class="muted">Used on the public accommodations page and related-room cards.</p></div>
        <label>Name<input name="name" value="{{ old('name', $accommodation->name) }}" maxlength="150" required><small>The website URL is generated automatically when you create the accommodation.</small></label>
        <div class="form-columns">
            <label>Category / eyebrow<input name="category" value="{{ old('category', $accommodation->category) }}" maxlength="150" required></label>
            <label>Listing heading<input name="heading" value="{{ old('heading', $accommodation->heading) }}" maxlength="255" required></label>
        </div>
        <label>Short description<textarea name="short_description" rows="3" maxlength="1000" required>{{ old('short_description', $accommodation->short_description) }}</textarea></label>
        <label>Listing highlights<textarea name="highlights_text" rows="4" maxlength="3000" required>{{ $highlights }}</textarea><small>Enter one highlight per line, up to 20.</small></label>
    </section>

    <section class="panel form-panel">
        <div><p class="eyebrow">IMAGE</p><h2>Accommodation image</h2><p class="muted">Shown on both the listing and detail pages.</p></div>
        <div class="image-picker" data-image-picker>
            <span class="image-picker-label">Room image</span>
            <div class="image-picker-body">
                <img class="image-picker-preview" data-image-preview @if($accommodation->imageUrl()) src="{{ $accommodation->imageUrl() }}" @else hidden @endif alt="Current accommodation image">
                <div class="image-picker-controls">
                    <label>Choose image from device<input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-input @required(!$editing)><small>JPG, PNG, or WebP. Maximum 2 MB and 6000 × 6000 pixels.</small></label>
                    <p class="image-picker-feedback muted" data-image-feedback aria-live="polite"></p>
                </div>
            </div>
        </div>
        <label>Image description<input name="image_alt" value="{{ old('image_alt', $accommodation->image_alt) }}" maxlength="255" required><small>Describe the image for visitors using screen readers.</small></label>
    </section>

    <section class="panel form-panel">
        <div><p class="eyebrow">DETAIL PAGE</p><h2>Room information</h2><p class="muted">Displayed after a visitor opens this accommodation.</p></div>
        <label>Full description<textarea data-wysiwyg name="description" rows="6" maxlength="5000" required>{{ old('description', $accommodation->description) }}</textarea></label>
        <label>Room features<textarea name="features_text" rows="6" maxlength="5000" required>{{ $features }}</textarea><small>Enter one feature per line, up to 20.</small></label>
        <label>Who it may suit<textarea data-wysiwyg name="ideal_for" rows="4" maxlength="3000" required>{{ old('ideal_for', $accommodation->ideal_for) }}</textarea></label>
        <label>Before you book<textarea data-wysiwyg name="stay_note" rows="4" maxlength="3000" required>{{ old('stay_note', $accommodation->stay_note) }}</textarea></label>
    </section>

    <section class="panel form-panel">
        <div><p class="eyebrow">VISIBILITY</p><h2>Publishing and order</h2></div>
        <div class="form-columns">
            <label>Display order<input type="number" name="sort_order" value="{{ old('sort_order', $accommodation->sort_order) }}" min="0" max="9999" required><small>Lower numbers appear first.</small></label>
            <label class="checkbox accommodation-publish"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $accommodation->is_published))> Published on the website</label>
        </div>
    </section>

    <div class="about-save-bar">
        <p class="muted">Saving updates both the public listing and detail page. Draft accommodations stay hidden.</p>
        <div class="about-save-actions">
            <a class="button button-outline" href="{{ route('admin.accommodations.index') }}">Cancel</a>
            <button type="submit" class="button">{{ $editing ? 'Save accommodation' : 'Create accommodation' }} →</button>
        </div>
    </div>
</form>
@endsection
