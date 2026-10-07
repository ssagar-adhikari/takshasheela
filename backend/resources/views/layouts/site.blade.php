<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', $siteSettings['default_meta_description'])">
    <meta name="theme-color" content="#173f36">
    <title>@yield('title', $siteSettings['site_name']) | {{ $siteSettings['site_name'] }}</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&amp;family=Newsreader:opsz,wght@6..72,400;6..72,500&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v=17">
    <script src="{{ asset('assets/js/site.js') }}?v=3" defer></script>
</head>
<body class="site-theme-@yield('theme', 'light')">
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header" data-header>
    <div class="shell site-header__inner">
        <a class="brand" href="{{ route('home') }}" aria-label="{{ $siteSettings['site_name'] }} home">
            <img src="{{ $siteSettings['logo_url'] }}" width="290" height="80" alt="{{ $siteSettings['logo_alt'] ?: $siteSettings['site_name'] }}">
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation" data-nav-toggle>
            <span></span><span></span><span></span><span class="sr-only">Menu</span>
        </button>
        <nav class="site-nav" id="site-navigation" aria-label="Primary navigation" data-nav>
            <ul>
                <li><a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a></li>
                <li class="nav-group">
                    <details>
                        <summary @class(['is-active' => request()->routeIs('about', 'ayurveda', 'team', 'approach')])>Discover</summary>
                        <div class="nav-panel">
                            <p class="nav-panel__label">Discover us</p>
                            <a href="{{ route('about') }}"><strong>About Takshasheela</strong><span>Our story, purpose, and values</span></a>
                            <a href="{{ route('ayurveda') }}"><strong>About Ayurveda</strong><span>Ancient wisdom for modern life</span></a>
                            <a href="{{ route('team') }}"><strong>Our Team</strong><span>Meet our leaders and practitioners</span></a>
                            <a href="{{ route('approach') }}"><strong>Our Approach</strong><span>How personalised care unfolds</span></a>
                        </div>
                    </details>
                </li>
                <li class="nav-group nav-group--wide">
                    <details>
                        <summary @class(['is-active' => request()->routeIs('programs.*', 'therapies.*', 'trainings.*', 'wellness.*')])>Wellness Programs</summary>
                        <div class="nav-panel nav-panel--wellness">
                            <p class="nav-panel__label">Choose a wellness path</p>
                            @forelse($wellnessCategories as $wellnessCategory)
                                <details class="nav-subgroup">
                                    <summary><span><strong>{{ $wellnessCategory->name }}</strong><small>{{ $wellnessCategory->nav_description }}</small></span></summary>
                                    <div class="nav-submenu">
                                        <a href="{{ $wellnessCategory->publicUrl() }}"><strong>All {{ $wellnessCategory->name }}</strong><span>Explore every option</span></a>
                                        @foreach($wellnessCategory->subcategories as $subcategory)
                                            <details class="nav-item-group">
                                                <summary>{{ $subcategory->name }}</summary>
                                                <div class="nav-item-list">
                                                    <a href="{{ $wellnessCategory->publicUrl() }}#{{ $subcategory->slug }}"><strong>All {{ $subcategory->name }}</strong>@if($subcategory->nav_description)<span>{{ $subcategory->nav_description }}</span>@endif</a>
                                                    @foreach($subcategory->offerings as $offering)
                                                        <a href="{{ $offering->publicUrl() }}"><strong>{{ $offering->name }}</strong></a>
                                                    @endforeach
                                                </div>
                                            </details>
                                        @endforeach
                                    </div>
                                </details>
                            @empty
                                <p>No wellness categories are published yet.</p>
                            @endforelse
                        </div>
                    </details>
                </li>
                <li><a href="{{ route('accommodations.index') }}" @if(request()->routeIs('accommodations.*')) aria-current="page" @endif>Accommodations</a></li>
                <li><a href="{{ route('products.index') }}" @if(request()->routeIs('products.*')) aria-current="page" @endif>Products</a></li>
                <li class="nav-group">
                    <details>
                        <summary @class(['is-active' => request()->routeIs('testimonials', 'blogs.*', 'news.*', 'chronicles.*', 'gallery')])>Chronicles</summary>
                        <div class="nav-panel">
                            <p class="nav-panel__label">Explore stories</p>
                            <a href="{{ route('testimonials') }}"><strong>Testimonials</strong><span>Guest reflections</span></a>
                            <a href="{{ route('blogs.index') }}"><strong>Blogs</strong><span>Ideas for conscious living</span></a>
                            <a href="{{ route('news.index') }}"><strong>News &amp; Events</strong><span>What is happening at the Aashram</span></a>
                            <a href="{{ route('gallery') }}"><strong>Gallery</strong><span>Moments of healing and serenity</span></a>
                        </div>
                    </details>
                </li>
            </ul>
            <a class="button button--small" href="{{ route('contact') }}">Contact</a>
        </nav>
    </div>
</header>
<main id="main-content">
    @yield('content')
</main>
<footer class="site-footer">
    <div class="shell footer-grid">
        <div class="footer-intro">
            <img src="{{ $siteSettings['logo_url'] }}" width="290" height="80" alt="{{ $siteSettings['logo_alt'] ?: $siteSettings['site_name'] }}">
            <p>{{ $siteSettings['business_description'] ?: $siteSettings['tagline'] }}</p>
            @if($siteSettings['tagline'])<p class="footer-tagline">{{ $siteSettings['tagline'] }}</p>@endif
        </div>
        <div><p class="footer-title">Explore</p><a href="{{ route('about') }}">Our story</a><a href="{{ route('approach') }}">Our approach</a><a href="{{ route('team') }}">Our team</a><a href="{{ route('gallery') }}">Gallery</a></div>
        <div><p class="footer-title">Visit</p><a href="{{ route('programs.index') }}">Wellness programs</a><a href="{{ route('therapies.index') }}">Therapies</a><a href="{{ route('trainings.index') }}">Training</a><a href="{{ route('accommodations.index') }}">Accommodation</a><a href="{{ route('products.index') }}">Ayurvedic products</a><a href="{{ route('contact') }}">Contact us</a></div>
        <div class="footer-contact"><p class="footer-title">{{ $siteSettings['site_name'] }}</p>@if($siteSettings['address'] || $siteSettings['city'] || $siteSettings['country'])<p>@include('site.partials.business-address')</p>@endif<a href="mailto:{{ $siteSettings['contact_email'] }}">{{ $siteSettings['contact_email'] }}</a>@if($siteSettings['contact_phone'])<a href="tel:{{ preg_replace('/[^+0-9]/', '', $siteSettings['contact_phone']) }}">{{ $siteSettings['contact_phone'] }}</a>@endif
@if($siteSettings['business_hours'])<p class="footer-hours">{!! nl2br(e($siteSettings['business_hours'])) !!}</p>@endif</div>
    </div>
    <div class="shell footer-bottom"><p>&copy; {{ now()->year }} {{ $siteSettings['legal_name'] ?: $siteSettings['site_name'] }}</p>@include('site.partials.social-links', ['class' => 'footer-socials footer-bottom__socials'])<div class="footer-bottom__links"><a href="{{ route('contact') }}">Enquiries</a><a href="{{ route('login') }}">Admin</a></div></div>
</footer>
</body>
</html>
