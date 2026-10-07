@extends('layouts.admin')
@section('title', 'Overview')
@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">YOUR WEBSITE, AT A GLANCE</p>
        <h1>Welcome back, {{ explode(' ', auth()->user()->name)[0] }}.</h1>
        <p class="muted">Manage the homepage, enquiries, About Us content, wellness programs, accommodations, products, Chronicles, website settings, and your account.</p>
    </div>
    <a class="button" href="{{ route('admin.settings.edit') }}">Edit site settings →</a>
</div>
<section class="welcome-banner">
    <div>
        <span class="eyebrow">YOUR ADMINISTRATION WORKSPACE</span>
        <h2>A little care for your website.</h2>
        <p>Keep your site's details current and your account secure.</p>
        <a class="button button-light" href="{{ route('home') }}" target="_blank" rel="noopener">Visit website <span>↗</span></a>
    </div>
    <div class="banner-art" aria-hidden="true"><span>✳</span><i></i></div>
</section>
<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-heading">
            <div><h2>Site details</h2><p class="muted">Your saved website information.</p></div>
            <a href="{{ route('admin.settings.edit') }}">Edit settings →</a>
        </div>
        <dl class="site-details">
            @foreach (['site_name' => 'Site name', 'contact_email' => 'Public email', 'enquiry_email' => 'Enquiry recipient', 'contact_phone' => 'Primary phone', 'address' => 'Address', 'business_hours' => 'Business hours'] as $key => $label)
                <div><dt>{{ $label }}</dt><dd>{{ $settings[$key] ?? null ?: 'Not set' }}</dd></div>
            @endforeach
            <div><dt>Administrators</dt><dd>{{ $administrators }}</dd></div>
        </dl>
    </section>
    <section class="panel quick-links">
        <div class="panel-heading"><div><h2>Quick access</h2><p class="muted">Everything you need to manage your workspace.</p></div></div>
        <a href="{{ route('admin.home.edit') }}"><span class="quick-icon">⌂</span><span><strong>Homepage</strong><small>Edit the hero and philosophy sections</small></span><span>→</span></a>
        <a href="{{ route('admin.enquiries.index') }}"><span class="quick-icon">✉</span><span><strong>Enquiries</strong><small>{{ $newEnquiries }} new of {{ $enquiries }} saved submissions</small></span><span>→</span></a>
        <a href="{{ route('admin.about.edit', 'about-takshasheela') }}"><span class="quick-icon">◇</span><span><strong>About Us</strong><small>Edit four sections and upload images</small></span><span>→</span></a>
        <a href="{{ route('admin.wellness-offerings.index') }}"><span class="quick-icon">✦</span><span><strong>Wellness Programs</strong><small>Manage {{ $wellness }} child {{ Str::plural('program', $wellness) }} and their categories</small></span><span>→</span></a>
        <a href="{{ route('admin.accommodations.index') }}"><span class="quick-icon">⌂</span><span><strong>Accommodations</strong><small>Manage {{ $accommodations }} room {{ Str::plural('listing', $accommodations) }}</small></span><span>→</span></a>
        <a href="{{ route('admin.products.index') }}"><span class="quick-icon">◈</span><span><strong>Products</strong><small>Manage {{ $products }} catalogue {{ Str::plural('item', $products) }}</small></span><span>→</span></a>
        <a href="{{ route('admin.chronicles.index') }}"><span class="quick-icon">✎</span><span><strong>Chronicles</strong><small>Manage {{ $chronicles }} {{ Str::plural('article', $chronicles) }}, testimonials, and gallery</small></span><span>→</span></a>
        <a href="{{ route('admin.settings.edit') }}"><span class="quick-icon">⚙</span><span><strong>Site settings</strong><small>Update brand, contact, map, and business details</small></span><span>→</span></a>
        <a href="{{ route('admin.profile.edit') }}"><span class="quick-icon">◎</span><span><strong>My account</strong><small>Manage your profile and password</small></span><span>→</span></a>
    </section>
</div>
@endsection
