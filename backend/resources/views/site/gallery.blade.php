@extends('layouts.site')
@section('title', 'Gallery')
@section('description', 'See the spaces, therapies, rooms, and quiet moments that shape Takshasheela.')
@section('content')
@include('site.partials.hero', ['eyebrow' => 'A glimpse inside', 'heading' => 'Spaces made for quieter days.', 'copy' => 'See the atmosphere, people, and simple details that support a restorative stay.', 'variant' => 'hero--about'])
<section class="section"><div class="shell"><div class="section-heading section-heading--center" data-reveal><p class="eyebrow">The Aashram</p><h2>Healing has a texture, a pace, and a place.</h2><p class="lede">Warm light, natural materials, attentive hands, nourishing rituals, and room to breathe.</p></div>@if($images->isNotEmpty())<div class="gallery-grid">@foreach($images as $image)<button class="gallery-item" type="button" data-lightbox="{{ $image->imageUrl() }}" data-reveal><img src="{{ $image->imageUrl() }}" alt="{{ $image->image_alt }}" loading="lazy"></button>@endforeach</div>@else<p>No gallery images are published yet.</p>@endif</div></section>
@if($images->isNotEmpty())<dialog class="gallery-dialog" id="gallery-dialog"><button class="dialog-close" type="button" data-dialog-close aria-label="Close image">×</button><img src="{{ $images->first()->imageUrl() }}" alt="{{ $images->first()->image_alt }}"></dialog>@endif
@include('site.partials.cta', ['eyebrow' => 'Picture yourself here', 'heading' => 'Talk with our team about dates, programs, and the kind of stay you need.'])
@endsection
