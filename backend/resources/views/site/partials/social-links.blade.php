@php
    $socialProfiles = collect([
        'facebook_url' => 'Facebook',
        'instagram_url' => 'Instagram',
        'youtube_url' => 'YouTube',
        'linkedin_url' => 'LinkedIn',
    ])->filter(fn ($label, $key) => filled($siteSettings[$key] ?? null));
@endphp
@if($socialProfiles->isNotEmpty())
<div class="{{ $class ?? 'contact-socials' }}" aria-label="Social media">
    @foreach($socialProfiles as $key => $label)
        <a class="social-icon" href="{{ $siteSettings[$key] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $siteSettings['site_name'] }} on {{ $label }}" title="{{ $label }}">
            @switch($key)
                @case('facebook_url')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.3 0-5 2-5 5v2H6v4h3v7h4v-7h3.5l.5-4h-4V9c0-.7.3-1 1-1Z"/></svg>
                    @break
                @case('instagram_url')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.25"/><circle cx="17.4" cy="6.7" r="1" class="social-icon-fill"/></svg>
                    @break
                @case('youtube_url')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 7.3a3 3 0 0 0-2.1-2.1C17 4.7 12 4.7 12 4.7s-5 0-6.9.5A3 3 0 0 0 3 7.3 31 31 0 0 0 2.5 12 31 31 0 0 0 3 16.7a3 3 0 0 0 2.1 2.1c1.9.5 6.9.5 6.9.5s5 0 6.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .5-4.7 31 31 0 0 0-.5-4.7Z"/><path d="m10 15.5 5.2-3.5L10 8.5v7Z" class="social-icon-play"/></svg>
                    @break
                @case('linkedin_url')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8.5h4V21H5V8.5Zm2-6A2.3 2.3 0 1 1 7 7a2.3 2.3 0 0 1 0-4.5ZM11 8.5h3.8v1.7h.1c.5-1 1.8-2.2 3.7-2.2 4 0 4.7 2.6 4.7 6V21h-4v-6.2c0-1.5 0-3.4-2.1-3.4s-2.4 1.6-2.4 3.3V21H11V8.5Z"/></svg>
                    @break
            @endswitch
            <span class="sr-only">{{ $label }}</span>
        </a>
    @endforeach
</div>
@endif
