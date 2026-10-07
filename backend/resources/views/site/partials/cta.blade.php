<section class="cta-band">
    <div class="shell cta-band__inner" data-reveal>
        <div><p class="eyebrow eyebrow--light">{{ $eyebrow }}</p><h2>{{ $heading }}</h2><x-rich-text class="cta-band__copy" :content="$copy ?? 'Tell us what you need. We will help you choose a thoughtful next step.'" /></div>
        <a class="button button--cream" href="{{ $href ?? route('contact') }}">Start a conversation</a>
    </div>
</section>
