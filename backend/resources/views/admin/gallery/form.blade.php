@extends('layouts.admin')
@php($editing = $item->exists)
@section('title', $editing ? 'Edit gallery image' : 'Add gallery image')
@section('content')
<div class="page-heading"><div><p class="eyebrow">GALLERY</p><h1>{{ $editing ? 'Edit gallery image' : 'Add gallery image' }}</h1><p class="muted">Upload an image and control its public order.</p></div><a class="button button-outline" href="{{ route('admin.gallery.index') }}">← Gallery</a></div>
<form method="post" action="{{ $editing ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}" enctype="multipart/form-data" class="stack about-editor">
    @csrf @if($editing) @method('PUT') @endif
    <section class="panel form-panel">
        <div class="image-picker" data-image-picker><span class="image-picker-label">Gallery image</span><div class="image-picker-body"><img class="image-picker-preview" data-image-preview @if($editing) src="{{ $item->imageUrl() }}" @else hidden @endif alt="Current gallery image"><div class="image-picker-controls"><label>Choose image from device<input type="file" name="image" accept="image/jpeg,image/png,image/webp" data-image-input @required(!$editing)><small>JPG, PNG, or WebP. Maximum 2 MB and 6000 × 6000 pixels.</small></label><p class="image-picker-feedback muted" data-image-feedback aria-live="polite"></p></div></div></div>
        <label>Image description<input name="image_alt" value="{{ old('image_alt', $item->image_alt) }}" maxlength="255" required><small>Required for visitors using screen readers.</small></label>
        <label>Caption<input name="caption" value="{{ old('caption', $item->caption) }}" maxlength="255"></label>
        <div class="form-columns"><label>Display order<input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" min="0" max="9999" required></label><label class="checkbox accommodation-publish"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))> Published on the website</label></div>
    </section>
    <div class="about-save-bar"><p class="muted">Draft images stay hidden from the public gallery.</p><div class="about-save-actions"><a class="button button-outline" href="{{ route('admin.gallery.index') }}">Cancel</a><button class="button" type="submit">{{ $editing ? 'Save image' : 'Add image' }} →</button></div></div>
</form>
@endsection
