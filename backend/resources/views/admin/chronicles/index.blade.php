@extends('layouts.admin')
@section('title', 'Blogs & News')
@section('content')
<div class="page-heading">
    <div><p class="eyebrow">CHRONICLES</p><h1>Blogs &amp; News</h1><p class="muted">Manage Chronicle listing cards and article detail pages.</p></div>
    <a class="button" href="{{ route('admin.chronicles.create', ['type' => $type ?: 'blog']) }}">+ Add article</a>
</div>
<nav class="about-tabs" aria-label="Filter Chronicle articles">
    <a href="{{ route('admin.chronicles.index') }}" @if(!$type) aria-current="page" @endif>All articles</a>
    <a href="{{ route('admin.chronicles.index', ['type' => 'blog']) }}" @if($type === 'blog') aria-current="page" @endif>Blogs</a>
    <a href="{{ route('admin.chronicles.index', ['type' => 'news']) }}" @if($type === 'news') aria-current="page" @endif>News &amp; Events</a>
</nav>
<section class="panel">
    <div class="panel-heading">
        <div><h2>Chronicle articles</h2><p class="muted">{{ $articles->total() }} {{ $articles->total() === 1 ? 'article' : 'articles' }}</p></div>
        <div class="panel-links"><a href="{{ route('blogs.index') }}" target="_blank" rel="noopener">Blogs ↗</a><a href="{{ route('news.index') }}" target="_blank" rel="noopener">News ↗</a></div>
    </div>
    @if($articles->isEmpty())
        <div class="empty-state"><div class="empty-icon">✎</div><h3>No articles yet</h3><p>Add the first Blog or News article.</p><a class="button" href="{{ route('admin.chronicles.create') }}">Add article</a></div>
    @else
        <div class="table-wrap"><table>
            <thead><tr><th>Article</th><th>Section</th><th>Status</th><th>Order</th><th>Updated</th><th><span class="sr-only">Actions</span></th></tr></thead>
            <tbody>
            @foreach($articles as $article)
                <tr>
                    <td><div class="chronicle-table-title">@if($article->imageUrl())<img class="chronicle-thumb" src="{{ $article->imageUrl() }}" alt="">@endif<div><span class="table-title">{{ $article->title }}</span><small>{{ $article->category }} · /{{ $article->slug }} @if($article->is_featured) · Featured @endif</small></div></div></td>
                    <td>{{ $article->type === 'blog' ? 'Blog' : 'News & Events' }}</td>
                    <td><span class="badge {{ $article->is_published ? 'badge-published' : 'badge-draft' }}">{{ $article->is_published ? 'Published' : 'Draft' }}</span></td>
                    <td>{{ $article->sort_order }}</td>
                    <td>{{ $article->updated_at->diffForHumans() }}</td>
                    <td><div class="table-actions">
                        <a href="{{ route('admin.chronicles.edit', $article) }}">Edit</a>
                        @if($article->is_published)<a href="{{ route('chronicles.show', $article->slug) }}" target="_blank" rel="noopener">View ↗</a>@endif
                        <form method="post" action="{{ route('admin.chronicles.destroy', $article) }}" onsubmit="return confirm('Delete this article? This cannot be undone.')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete</button></form>
                    </div></td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
        @if($articles->hasPages())<div class="pagination">{{ $articles->links() }}</div>@endif
    @endif
</section>
@endsection
