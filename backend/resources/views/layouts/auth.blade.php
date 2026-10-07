<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ $siteSettings['site_name'] }} CMS</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=11">
</head>
<body class="auth-body">
    <main class="auth-layout">
        <section class="auth-story" aria-label="{{ $siteSettings['site_name'] }}">
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark">{{ mb_strtoupper(mb_substr($siteSettings['site_name'], 0, 1)) }}</span><span>{{ $siteSettings['site_name'] }}<small>CONTENT MANAGEMENT</small></span></a>
            <div><span class="eyebrow">{{ $siteSettings['tagline'] ?: 'A little care goes a long way' }}</span><h1>A thoughtful space.<br>A meaningful story.</h1><p>{{ $siteSettings['business_description'] }}</p></div>
            <span class="story-footer">{{ $siteSettings['tagline'] ?: $siteSettings['site_name'] }}</span>
        </section>
        <section class="auth-panel"><div class="auth-card">
            <span class="eyebrow">{{ mb_strtoupper($siteSettings['site_name']) }} CMS</span>
            <h1>@yield('title')</h1><p class="muted">@yield('description')</p>
            @include('partials.messages')
            @yield('content')
            <a class="back-link" href="{{ route('home') }}">← Back to website</a>
        </div><p class="auth-footer">© {{ date('Y') }} {{ $siteSettings['legal_name'] ?: $siteSettings['site_name'] }}</p></section>
    </main>
</body>
</html>
