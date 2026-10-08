@php
    $seo = \App\Support\Seo::metadata($__env->yieldContent('title'), $__env->yieldContent('description'), $siteSettings, get_defined_vars());
@endphp
<title>{{ $seo['pageTitle'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="robots" content="index, follow, max-image-preview:large">
<link rel="canonical" href="{{ $seo['canonical'] }}">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="{{ $siteSettings['site_name'] }}">
<meta property="og:type" content="{{ $seo['type'] }}">
<meta property="og:title" content="{{ $seo['pageTitle'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:image:alt" content="{{ $seo['imageAlt'] }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['pageTitle'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">
<meta name="twitter:image:alt" content="{{ $seo['imageAlt'] }}">
@if($seo['article']?->published_at)
<meta property="article:published_time" content="{{ $seo['article']->published_at->toAtomString() }}">
@endif
@if($seo['article']?->updated_at)
<meta property="article:modified_time" content="{{ $seo['article']->updated_at->toAtomString() }}">
@endif
<script type="application/ld+json">{!! $seo['json'] !!}</script>
