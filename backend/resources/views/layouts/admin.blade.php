<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><meta name="robots" content="noindex, nofollow">
<title>@yield('title', 'Dashboard') · {{ $siteSettings['site_name'] }} CMS</title>
<script>try{if(localStorage.getItem('takshasheela-sidebar-collapsed')==='true')document.documentElement.classList.add('sidebar-is-collapsed')}catch(error){}</script>
<link rel="stylesheet" href="{{ asset('vendor/quill/quill.snow.css') }}?v=2.0.3">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=19">
<script src="{{ asset('vendor/quill/quill.js') }}?v=2.0.3" defer></script>
<script src="{{ asset('js/admin.js') }}?v=8" defer></script>
</head>
<body class="admin-body">
<a class="skip-link" href="#main">Skip to content</a>
<aside class="sidebar" id="sidebar">
<a class="brand" href="{{ route('admin.dashboard') }}"><span class="admin-brand-logo"><img src="{{ $siteSettings['logo_url'] }}" alt="{{ $siteSettings['logo_alt'] ?: $siteSettings['site_name'].' logo' }}"></span><span>{{ $siteSettings['site_name'] }}<small>ADMINISTRATION</small></span></a>
<p class="nav-label">WORKSPACE</p>
<nav aria-label="Administration">
<a href="{{ route('admin.dashboard') }}" title="Overview" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">▦</span><span class="nav-text">Overview</span></a>
<a href="{{ route('admin.home.edit') }}" title="Homepage" @if(request()->routeIs('admin.home.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">⌂</span><span class="nav-text">Homepage</span></a>
<a href="{{ route('admin.users.index') }}" title="Users" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">♙</span><span class="nav-text">Users</span></a>
<a href="{{ route('admin.enquiries.index') }}" title="Enquiries" @if(request()->routeIs('admin.enquiries.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">✉</span><span class="nav-text">Enquiries</span></a>
<div class="nav-group">
<button class="nav-group-toggle" type="button" aria-controls="about-menu-items" aria-expanded="{{ request()->routeIs('admin.about.*') ? 'true' : 'false' }}" data-nav-group-toggle><span class="nav-group-title">ABOUT US</span><span class="nav-group-chevron" aria-hidden="true">⌄</span></button>
<div class="nav-group-items" id="about-menu-items" data-nav-group-items @if(!request()->routeIs('admin.about.*')) hidden @endif>
@foreach(config('about.sections') as $slug => $aboutMenu)
<a class="about-nav-link" href="{{ route('admin.about.edit', $slug) }}" title="{{ $aboutMenu['label'] }}" @if(request()->routeIs('admin.about.*') && request()->route('section') === $slug) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">◇</span><span class="nav-text">{{ $aboutMenu['label'] }}</span></a>
@endforeach
</div>
</div>
<div class="nav-group">
<button class="nav-group-toggle" type="button" aria-controls="wellness-menu-items" aria-expanded="{{ request()->routeIs('admin.wellness-categories.*', 'admin.wellness-subcategories.*', 'admin.wellness-offerings.*') ? 'true' : 'false' }}" data-nav-group-toggle><span class="nav-group-title">WELLNESS PROGRAMS</span><span class="nav-group-chevron" aria-hidden="true">⌄</span></button>
<div class="nav-group-items" id="wellness-menu-items" data-nav-group-items @if(!request()->routeIs('admin.wellness-categories.*', 'admin.wellness-subcategories.*', 'admin.wellness-offerings.*')) hidden @endif>
<a href="{{ route('admin.wellness-categories.index') }}" title="Wellness categories" @if(request()->routeIs('admin.wellness-categories.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">◇</span><span class="nav-text">Categories</span></a>
<a href="{{ route('admin.wellness-subcategories.index') }}" title="Wellness subcategories" @if(request()->routeIs('admin.wellness-subcategories.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">◈</span><span class="nav-text">Subcategories</span></a>
<a href="{{ route('admin.wellness-offerings.index') }}" title="All wellness programs" @if(request()->routeIs('admin.wellness-offerings.index', 'admin.wellness-offerings.edit')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">✦</span><span class="nav-text">All offerings</span></a>
<a href="{{ route('admin.wellness-offerings.create') }}" title="Add wellness program" @if(request()->routeIs('admin.wellness-offerings.create')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">＋</span><span class="nav-text">Add offering</span></a>
</div>
</div>
<div class="nav-group">
<button class="nav-group-toggle" type="button" aria-controls="accommodation-menu-items" aria-expanded="{{ request()->routeIs('admin.accommodations.*') ? 'true' : 'false' }}" data-nav-group-toggle><span class="nav-group-title">ACCOMMODATIONS</span><span class="nav-group-chevron" aria-hidden="true">⌄</span></button>
<div class="nav-group-items" id="accommodation-menu-items" data-nav-group-items @if(!request()->routeIs('admin.accommodations.*')) hidden @endif>
<a href="{{ route('admin.accommodations.index') }}" title="All accommodations" @if(request()->routeIs('admin.accommodations.index') || request()->routeIs('admin.accommodations.edit')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">⌂</span><span class="nav-text">All accommodations</span></a>
<a href="{{ route('admin.accommodations.create') }}" title="Add accommodation" @if(request()->routeIs('admin.accommodations.create')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">＋</span><span class="nav-text">Add accommodation</span></a>
</div>
</div>
<div class="nav-group">
<button class="nav-group-toggle" type="button" aria-controls="product-menu-items" aria-expanded="{{ request()->routeIs('admin.products.*') ? 'true' : 'false' }}" data-nav-group-toggle><span class="nav-group-title">PRODUCTS</span><span class="nav-group-chevron" aria-hidden="true">⌄</span></button>
<div class="nav-group-items" id="product-menu-items" data-nav-group-items @if(!request()->routeIs('admin.products.*')) hidden @endif>
<a href="{{ route('admin.products.index') }}" title="All products" @if(request()->routeIs('admin.products.index') || request()->routeIs('admin.products.edit')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">◈</span><span class="nav-text">All products</span></a>
<a href="{{ route('admin.products.create') }}" title="Add product" @if(request()->routeIs('admin.products.create')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">＋</span><span class="nav-text">Add product</span></a>
</div>
</div>
<div class="nav-group">
<button class="nav-group-toggle" type="button" aria-controls="chronicle-menu-items" aria-expanded="{{ request()->routeIs('admin.chronicles.*', 'admin.testimonials.*', 'admin.gallery.*') ? 'true' : 'false' }}" data-nav-group-toggle><span class="nav-group-title">CHRONICLES</span><span class="nav-group-chevron" aria-hidden="true">⌄</span></button>
<div class="nav-group-items" id="chronicle-menu-items" data-nav-group-items @if(!request()->routeIs('admin.chronicles.*', 'admin.testimonials.*', 'admin.gallery.*')) hidden @endif>
<a href="{{ route('admin.chronicles.index', ['type' => 'blog']) }}" title="Blogs" @if(request()->routeIs('admin.chronicles.*') && request('type') !== 'news') aria-current="page" @endif><span class="nav-icon" aria-hidden="true">✎</span><span class="nav-text">Blogs &amp; News</span></a>
<a href="{{ route('admin.testimonials.index') }}" title="Testimonials" @if(request()->routeIs('admin.testimonials.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">“</span><span class="nav-text">Testimonials</span></a>
<a href="{{ route('admin.gallery.index') }}" title="Gallery" @if(request()->routeIs('admin.gallery.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">▧</span><span class="nav-text">Gallery</span></a>
</div>
</div>
<div class="nav-group">
<button class="nav-group-toggle" type="button" aria-controls="settings-menu-items" aria-expanded="{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.profile.*') ? 'true' : 'false' }}" data-nav-group-toggle><span class="nav-group-title">SETTINGS</span><span class="nav-group-chevron" aria-hidden="true">⌄</span></button>
<div class="nav-group-items" id="settings-menu-items" data-nav-group-items @if(!request()->routeIs('admin.settings.*') && !request()->routeIs('admin.profile.*')) hidden @endif>
<a href="{{ route('admin.settings.edit') }}" title="Site settings" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">⚙</span><span class="nav-text">Site settings</span></a>
<a href="{{ route('admin.profile.edit') }}" title="My account" @if(request()->routeIs('admin.profile.*')) aria-current="page" @endif><span class="nav-icon" aria-hidden="true">◎</span><span class="nav-text">My account</span></a>
</div>
</div>
</nav>
<div class="sidebar-bottom"><form method="post" action="{{ route('logout') }}">@csrf<button class="logout" type="submit" title="Sign out"><span class="logout-label">Sign out</span><span aria-hidden="true">↗</span></button></form></div>
</aside>
<div class="workspace">
<header class="topbar"><div class="topbar-start"><button class="menu-toggle" type="button" aria-controls="sidebar" aria-expanded="false" data-menu>☰ <span class="sr-only">Toggle navigation</span></button><button class="sidebar-collapse" type="button" aria-controls="sidebar" aria-expanded="true" title="Collapse menu" data-sidebar-collapse><span class="sidebar-collapse-icon" aria-hidden="true">‹</span><span class="sr-only">Collapse navigation</span></button><span class="breadcrumb">Workspace <span>/</span> <strong>@yield('title', 'Overview')</strong></span></div><a class="user-menu" href="{{ route('admin.profile.edit') }}"><span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span>{{ auth()->user()->name }}<small>Administrator</small></span></a></header>
<main id="main" class="main-content">
@include('partials.messages')
@yield('content')
</main><footer class="workspace-footer">{{ $siteSettings['legal_name'] ?: $siteSettings['site_name'] }} <span>{{ $siteSettings['tagline'] ?: 'Made for meaningful experiences.' }}</span></footer>
</div>
</body></html>
