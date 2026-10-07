@extends('layouts.site')
@section('title', $content['meta_title'])
@section('description', $content['meta_description'] ?? '')
@section('content')
@php
    $imageUrl = fn (?string $path) => ! $path ? null : (str_starts_with($path, 'assets/images/') ? asset($path) : asset('storage/'.$path));
    $blocks = array_values($content['blocks'] ?? []);
@endphp
<section class="page-hero about-managed-hero">
    @if($heroImage = $imageUrl($content['hero_image'] ?? null))<img class="about-hero-image" src="{{ $heroImage }}" alt="{{ $content['hero_image_alt'] ?? '' }}" fetchpriority="high">@endif
    <div class="shell page-hero__content" data-reveal><p class="eyebrow eyebrow--light">{{ $content['hero_eyebrow'] ?? '' }}</p><h1>{{ $content['hero_title'] }}</h1><x-rich-text :content="$content['hero_description'] ?? ''" /></div>
</section>
@for($i = 0; $i < count($blocks); $i++)
    @php $block = $blocks[$i]; @endphp
    @if($block['kind'] === 'split')
        @php $image = $imageUrl($block['image'] ?? null); @endphp
        <section class="section {{ $i % 2 === 0 ? 'section--cream' : '' }}"><div class="shell split {{ $i % 2 === 1 ? 'split--reverse' : '' }} {{ ! $image ? 'about-text-only' : '' }}">@if($image)<div class="split__media" data-reveal><img src="{{ $image }}" alt="{{ $block['image_alt'] ?? '' }}" loading="lazy"></div>@endif<div class="split__content" data-reveal><p class="eyebrow">{{ $block['eyebrow'] ?? '' }}</p><h2>{{ $block['title'] }}</h2><x-rich-text :content="$block['body']" /></div></div></section>
        @continue
    @endif
    @php
        $headingBlock = $block['kind'] === 'heading' ? $block : null;
        $kind = $headingBlock ? ($blocks[$i + 1]['kind'] ?? null) : $block['kind'];
        $group = [];
        if (in_array($kind, ['card', 'person', 'principle', 'text'], true)) {
            if ($headingBlock) { $i++; }
            do { $group[] = $blocks[$i]; $i++; } while (isset($blocks[$i]) && $blocks[$i]['kind'] === $kind);
            $i--;
        }
    @endphp
    <section class="section {{ in_array($kind, ['person', 'text'], true) ? 'section--cream' : '' }}"><div class="shell">
        @if($headingBlock)<div class="section-heading {{ $kind !== 'person' ? 'section-heading--center' : '' }}" data-reveal>@if($headingImage = $imageUrl($headingBlock['image'] ?? null))<img class="about-heading-image" src="{{ $headingImage }}" alt="{{ $headingBlock['image_alt'] ?? '' }}" loading="lazy">@endif<p class="eyebrow">{{ $headingBlock['eyebrow'] ?? '' }}</p><h2>{{ $headingBlock['title'] }}</h2><x-rich-text :content="$headingBlock['body']" /></div>@endif
        @if($group)<div class="{{ $kind === 'person' ? 'team-grid' : ($kind === 'text' ? 'split' : 'card-grid') }}">@foreach($group as $index => $entry)@php $entryImage = $imageUrl($entry['image'] ?? null); @endphp<article class="{{ match ($kind) { 'person' => 'person', 'principle' => 'number-card', 'text' => 'split__content', default => 'feature-card dosha-card' } }}" data-reveal>@if($entryImage)<img class="about-entry-image" src="{{ $entryImage }}" alt="{{ $entry['image_alt'] ?: $entry['title'] }}" loading="lazy">@endif @if($kind === 'principle')<span class="number-card__number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>@endif<div @class(['feature-card__body' => $kind === 'card'])>@if($entry['eyebrow'])<p class="eyebrow">{{ $entry['eyebrow'] }}</p>@endif<h3>{{ $entry['title'] }}</h3><x-rich-text :content="$entry['body']" /></div></article>@endforeach</div>@endif
    </div></section>
@endfor
@include('site.partials.cta', ['eyebrow' => $content['cta_eyebrow'] ?? '', 'heading' => $content['cta_title'], 'copy' => $content['cta_body'] ?? ''])
@endsection
