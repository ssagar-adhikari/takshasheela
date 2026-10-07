@extends('layouts.admin')
@php($editing = $testimonial->exists)
@section('title', $editing ? 'Edit testimonial' : 'Add testimonial')
@section('content')
<div class="page-heading"><div><p class="eyebrow">TESTIMONIAL</p><h1>{{ $editing ? 'Edit testimonial' : 'Add testimonial' }}</h1><p class="muted">Guest reflections appear on the homepage and Testimonials page.</p></div><a class="button button-outline" href="{{ route('admin.testimonials.index') }}">← Testimonials</a></div>
<form method="post" action="{{ $editing ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" class="stack about-editor">
    @csrf @if($editing) @method('PUT') @endif
    <section class="panel form-panel">
        <div class="form-columns"><label>Guest name<input name="guest_name" value="{{ old('guest_name', $testimonial->guest_name) }}" maxlength="150" required></label><label>Location<input name="guest_location" value="{{ old('guest_location', $testimonial->guest_location) }}" maxlength="150"></label></div>
        <label>Testimonial title<input name="title" value="{{ old('title', $testimonial->title) }}" maxlength="255" required></label>
        <label>Guest quote<textarea name="quote" rows="7" maxlength="5000" required>{{ old('quote', $testimonial->quote) }}</textarea></label>
        <div class="form-columns"><label>Rating<select name="rating" required>@foreach(range(5, 1) as $rating)<option value="{{ $rating }}" @selected((int) old('rating', $testimonial->rating) === $rating)>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</option>@endforeach</select></label><label>Display order<input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" min="0" max="9999" required></label></div>
        <label class="checkbox accommodation-publish"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $testimonial->is_published))> Published on the website</label>
    </section>
    <div class="about-save-bar"><p class="muted">Draft testimonials stay hidden.</p><div class="about-save-actions"><a class="button button-outline" href="{{ route('admin.testimonials.index') }}">Cancel</a><button class="button" type="submit">{{ $editing ? 'Save testimonial' : 'Create testimonial' }} →</button></div></div>
</form>
@endsection
