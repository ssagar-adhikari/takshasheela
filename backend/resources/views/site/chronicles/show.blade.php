@extends('layouts.site')
@section('title', $article->title)
@section('description', $article->excerpt)
@section('theme', 'detail')
@section('content')
<section class="offering-detail section"><div class="shell offering-detail__grid"><div class="offering-detail__media" data-reveal><img src="{{ $article->imageUrl() }}" alt="{{ $article->image_alt }}"></div><div class="offering-detail__content" data-reveal><nav class="breadcrumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ $article->type === 'news' ? route('news.index') : route('blogs.index') }}">{{ $article->type === 'news' ? 'News & Events' : 'Blogs' }}</a><span>/</span><span>{{ $article->title }}</span></nav><p class="eyebrow">{{ $article->category }}</p><h1>{{ $article->title }}</h1>@if($article->published_at)<p class="chronicle-date">{{ $article->published_at->format('F j, Y') }}</p>@endif<p class="offering-detail__lead">{{ $article->excerpt }}</p></div></div></section>
<section class="section section--cream"><article class="shell chronicle-article-body"><x-rich-text :content="$article->body" /></article></section>
@include('site.partials.cta', ['eyebrow' => 'Continue your journey', 'heading' => 'Talk with our team about retreats, gatherings, and personalised care.'])
@endsection
