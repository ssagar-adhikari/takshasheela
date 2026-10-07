@extends('layouts.admin')
@section('title', $menu['label'])
@section('content')
@php
    $isTeam = $section->slug === 'our-team';
    $displayBlocks = $section->content['blocks'];
    if ($isTeam && is_array(old('content.blocks'))) {
        $displayBlocks = [];
        foreach (old('content.blocks') as $key => $values) {
            $original = $section->content['blocks'][$key] ?? [
                'kind' => 'person',
                'eyebrow' => '',
                'title' => '',
                'body' => '',
                'image' => null,
                'image_alt' => '',
            ];
            $displayBlocks[$key] = array_replace($original, $values);
        }
    }
@endphp
<div class="page-heading">
    <div><p class="eyebrow">ABOUT US</p><h1>{{ $menu['label'] }}</h1><p class="muted">Edit this section's content and choose images from your device.</p></div>
    <a class="button button-outline" href="{{ route($menu['route']) }}" target="_blank" rel="noopener">View website ↗</a>
</div>
<nav class="about-tabs" aria-label="About Us sections">
    @foreach(config('about.sections') as $slug => $item)
    <a href="{{ route('admin.about.edit', $slug) }}" @if($section->slug === $slug) aria-current="page" @endif>{{ $item['label'] }}</a>
    @endforeach
</nav>
<form method="post" action="{{ route('admin.about.update', $section->slug) }}" enctype="multipart/form-data" class="stack about-editor">
    @csrf @method('PUT')
    <section class="panel form-panel">
        <div><h2>Hero banner</h2><p class="muted">The opening image and introduction for {{ $menu['label'] }}.</p></div>
        <label>Eyebrow<input name="content[hero_eyebrow]" value="{{ old('content.hero_eyebrow', $section->content['hero_eyebrow']) }}" maxlength="150"></label>
        <label>Main heading<input name="content[hero_title]" value="{{ old('content.hero_title', $section->content['hero_title']) }}" maxlength="255" required></label>
        <label>Introduction<textarea data-wysiwyg name="content[hero_description]" rows="3" maxlength="1000">{{ old('content.hero_description', $section->content['hero_description']) }}</textarea></label>
        @include('admin.about.image', ['inputName' => 'hero_image', 'removeName' => 'remove_hero_image', 'path' => $section->content['hero_image'], 'label' => 'Hero image'])
        <label>Image description<input name="content[hero_image_alt]" value="{{ old('content.hero_image_alt', $section->content['hero_image_alt']) }}" maxlength="255"><small>Describe the image for visitors using screen readers.</small></label>
    </section>
    @foreach($displayBlocks as $key => $block)
    @continue($isTeam && $block['kind'] === 'person')
    <section class="panel form-panel">
        <div><p class="eyebrow">{{ $block['kind'] === 'person' ? 'TEAM MEMBER' : 'CONTENT SECTION' }}</p><h2>{{ $block['title'] }}</h2></div>
        <div class="form-columns">
            <label>{{ $block['kind'] === 'person' ? 'Role / subtitle' : 'Eyebrow' }}<input name="content[blocks][{{ $key }}][eyebrow]" value="{{ old('content.blocks.'.$key.'.eyebrow', $block['eyebrow']) }}" maxlength="150"></label>
            <label>{{ $block['kind'] === 'person' ? 'Full name' : 'Heading' }}<input name="content[blocks][{{ $key }}][title]" value="{{ old('content.blocks.'.$key.'.title', $block['title']) }}" maxlength="255" required></label>
        </div>
        <label>{{ $block['kind'] === 'person' ? 'Biography' : 'Description' }}<textarea data-wysiwyg name="content[blocks][{{ $key }}][body]" rows="{{ $block['kind'] === 'split' ? 7 : 4 }}" maxlength="20000">{{ old('content.blocks.'.$key.'.body', $block['body']) }}</textarea><small>Use the toolbar to format this content.</small></label>
        @include('admin.about.image', ['inputName' => 'block_images['.$key.']', 'removeName' => 'remove_images['.$key.']', 'path' => $block['image'], 'label' => $block['kind'] === 'person' ? 'Profile image' : 'Section image'])
        <label>Image description<input name="content[blocks][{{ $key }}][image_alt]" value="{{ old('content.blocks.'.$key.'.image_alt', $block['image_alt']) }}" maxlength="255"></label>
    </section>
    @endforeach
    @if($isTeam)
    <section class="panel form-panel team-members-panel" id="team-members">
        <div class="team-members-heading">
            <div><p class="eyebrow">OUR TEAM</p><h2>Team members</h2><p class="muted">Add as many people as you need, or remove members who should no longer appear.</p></div>
        </div>
        <div class="team-member-list" data-team-members>
            @foreach($displayBlocks as $key => $block)
                @if($block['kind'] === 'person')
                    @include('admin.about.team-member', ['memberKey' => $key, 'block' => $block])
                @endif
            @endforeach
        </div>
        <div class="team-members-empty" data-team-empty @if(collect($displayBlocks)->contains(fn ($block) => $block['kind'] === 'person')) hidden @endif>
            <p>No team members yet.</p><p class="muted">Use “Add team member” to create the first profile.</p>
        </div>
        <div class="team-member-add">
            <button class="button button-add-member" type="button" data-add-team-member><span class="button-add-member__icon" aria-hidden="true">+</span><span>Add team member</span></button>
        </div>
    </section>
    <template data-team-member-template>
        <article class="team-member-editor" data-team-member>
            <div class="team-member-editor-heading"><div><p class="eyebrow">TEAM MEMBER</p><h3 data-member-heading>New team member</h3></div><button class="text-danger team-member-remove" type="button" data-remove-team-member>Delete member</button></div>
            <div class="form-columns">
                <label>Role / subtitle<input name="content[blocks][__KEY__][eyebrow]" maxlength="150"></label>
                <label>Full name<input name="content[blocks][__KEY__][title]" maxlength="255" required data-member-name></label>
            </div>
            <label>Biography<textarea data-wysiwyg name="content[blocks][__KEY__][body]" rows="4" maxlength="20000"></textarea><small>Use the toolbar to format this content.</small></label>
            @include('admin.about.image', ['inputName' => 'block_images[__KEY__]', 'removeName' => 'remove_images[__KEY__]', 'path' => null, 'label' => 'Profile image'])
            <label>Image description<input name="content[blocks][__KEY__][image_alt]" maxlength="255"></label>
        </article>
    </template>
    @endif
    <section class="panel form-panel">
        <h2>Closing invitation</h2>
        <label>Eyebrow<input name="content[cta_eyebrow]" value="{{ old('content.cta_eyebrow', $section->content['cta_eyebrow']) }}" maxlength="150"></label>
        <label>Heading<input name="content[cta_title]" value="{{ old('content.cta_title', $section->content['cta_title']) }}" maxlength="500" required></label>
        <label>Description<textarea data-wysiwyg name="content[cta_body]" rows="3" maxlength="1000">{{ old('content.cta_body', $section->content['cta_body']) }}</textarea></label>
    </section>
    <section class="panel form-panel">
        <h2>Search appearance</h2>
        <label>Browser / SEO title<input name="content[meta_title]" value="{{ old('content.meta_title', $section->content['meta_title']) }}" maxlength="100" required></label>
        <label>Meta description<textarea name="content[meta_description]" rows="3" maxlength="300">{{ old('content.meta_description', $section->content['meta_description']) }}</textarea></label>
    </section>
    <div class="about-save-bar">
        <p class="muted">Changes appear on the website after saving. Upload up to 6 MB per save.</p>
        <div class="about-save-actions">
            <button type="submit" class="button">Save {{ $menu['label'] }} →</button>
        </div>
    </div>
</form>
@endsection
