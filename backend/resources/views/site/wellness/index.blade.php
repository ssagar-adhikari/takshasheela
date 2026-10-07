@extends('layouts.site')
@section('title', $category->hero_eyebrow)
@section('description', $category->hero_description)
@section('content')
<section class="page-hero about-managed-hero">
    @if($category->heroImageUrl())<img class="about-hero-image" src="{{ $category->heroImageUrl() }}" alt="{{ $category->hero_image_alt }}" fetchpriority="high">@endif
    <div class="shell page-hero__content" data-reveal><p class="eyebrow eyebrow--light">{{ $category->hero_eyebrow }}</p><h1>{{ $category->hero_title }}</h1><x-rich-text :content="$category->hero_description" /></div>
</section>
<section class="section"><div class="shell"><div class="section-heading" data-reveal><p class="eyebrow">{{ $category->section_eyebrow }}</p><h2>{{ $category->section_title }}</h2></div>
@forelse($subcategories as $subcategory)
<section class="wellness-subcategory" id="{{ $subcategory->slug }}" data-reveal><div class="wellness-subcategory__heading"><p class="eyebrow">{{ $category->name }}</p><h3>{{ $subcategory->name }}</h3>@if($subcategory->nav_description)<p>{{ $subcategory->nav_description }}</p>@endif</div><div class="card-grid">
@foreach($subcategory->offerings as $offering)<article class="feature-card"><img src="{{ $offering->imageUrl() }}" alt="{{ $offering->image_alt }}" loading="lazy"><div class="feature-card__body"><p class="eyebrow">{{ $offering->eyebrow }}</p><h3>{{ $offering->name }}</h3><p>{{ $offering->short_description }}</p><ul class="feature-list">@foreach($offering->highlights as $highlight)<li>{{ $highlight }}</li>@endforeach</ul><a class="text-link" href="{{ $offering->publicUrl() }}">View details</a></div></article>@endforeach
</div></section>
@empty<p>No subcategories or offerings are published in this category yet.</p>@endforelse
</div></section>
@if($category->information_title)<section class="section section--ink"><div class="shell intro-grid" data-reveal><div><p class="eyebrow eyebrow--light">{{ $category->information_eyebrow }}</p><h2>{{ $category->information_title }}</h2></div><div class="intro-copy"><x-rich-text :content="$category->information_body" /></div></div></section>@endif
@include('site.partials.cta', ['eyebrow' => $category->cta_eyebrow, 'heading' => $category->cta_title, 'copy' => $category->cta_body, 'href' => route('contact', ['type' => 'wellness:'.$category->slug])])
@endsection
