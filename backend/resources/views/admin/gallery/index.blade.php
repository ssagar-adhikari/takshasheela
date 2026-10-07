@extends('layouts.admin')
@section('title', 'Gallery')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CHRONICLES</p><h1>Gallery</h1><p class="muted">Manage images shown in the public lightbox gallery.</p></div><a class="button" href="{{ route('admin.gallery.create') }}">+ Add image</a></div>
<section class="panel">
    <div class="panel-heading"><div><h2>Gallery images</h2><p class="muted">{{ $items->total() }} images</p></div><a href="{{ route('gallery') }}" target="_blank" rel="noopener">View website ↗</a></div>
    @if($items->isEmpty())
        <div class="empty-state"><div class="empty-icon">▧</div><h3>No gallery images yet</h3><a class="button" href="{{ route('admin.gallery.create') }}">Add image</a></div>
    @else
        <div class="admin-gallery-grid">
        @foreach($items as $item)
            <article class="admin-gallery-card"><img src="{{ $item->imageUrl() }}" alt=""><div><strong>{{ $item->caption ?: 'Untitled image' }}</strong><small>{{ $item->image_alt }}</small><span class="badge {{ $item->is_published ? 'badge-published' : 'badge-draft' }}">{{ $item->is_published ? 'Published' : 'Draft' }}</span><div class="table-actions"><a href="{{ route('admin.gallery.edit', $item) }}">Edit</a><form method="post" action="{{ route('admin.gallery.destroy', $item) }}" onsubmit="return confirm('Delete this gallery image?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete</button></form></div></div></article>
        @endforeach
        </div>
        @if($items->hasPages())<div class="pagination">{{ $items->links() }}</div>@endif
    @endif
</section>
@endsection
