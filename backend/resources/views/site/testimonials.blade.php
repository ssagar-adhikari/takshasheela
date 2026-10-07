@extends('layouts.site')
@section('title', 'Testimonials')
@section('description', 'Guest reflections on healing, renewal, and inner clarity at Takshasheela Ayurveda Aashram.')
@section('content')
@include('site.partials.hero', ['eyebrow' => 'Guest reflections', 'heading' => 'Stories of healing, renewal, and lasting peace from our guests.', 'copy' => 'These heartfelt reflections share the calm, transformation, and inner clarity experienced at Takshasheela Ayurveda Aashram.', 'variant' => 'hero--water'])
<section class="section section--cream"><div class="shell"><div class="section-heading section-heading--center"><p class="eyebrow">Guest reflections</p><h2>In their own words.</h2></div><div class="card-grid">@forelse($testimonials as $testimonial)<article class="testimonial-card"><p class="testimonial-card__rating" aria-label="{{ $testimonial->rating }} out of five stars">{{ str_repeat('★', $testimonial->rating) }}</p><h3>{{ $testimonial->title }}</h3><blockquote>“{{ $testimonial->quote }}”</blockquote><footer><span>{{ $testimonial->initials() }}</span><p><strong>{{ $testimonial->guest_name }}</strong>{{ $testimonial->guest_location }}</p></footer></article>@empty<p>No guest reflections are published yet.</p>@endforelse</div></div></section>
@include('site.partials.cta', ['eyebrow' => 'Begin your story', 'heading' => 'A restorative stay starts with a thoughtful conversation.'])
@endsection
