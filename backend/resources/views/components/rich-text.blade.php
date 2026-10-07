@props(['content'])
@if(trim(strip_tags((string) $content)) !== '')
<div {{ $attributes->class('rich-text') }}>{!! \App\Support\RichText::render($content) !!}</div>
@endif
