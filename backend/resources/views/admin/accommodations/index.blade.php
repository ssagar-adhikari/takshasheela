@extends('layouts.admin')
@section('title', 'Accommodations')
@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">STAY</p>
        <h1>Accommodations</h1>
        <p class="muted">Manage the room listings and the content shown on every accommodation detail page.</p>
    </div>
    <a class="button" href="{{ route('admin.accommodations.create') }}">+ Add accommodation</a>
</div>
<section class="panel">
    <div class="panel-heading">
        <div><h2>Room listings</h2><p class="muted">{{ $accommodations->total() }} {{ $accommodations->total() === 1 ? 'accommodation' : 'accommodations' }}</p></div>
        <a href="{{ route('accommodations.index') }}" target="_blank" rel="noopener">View listing page ↗</a>
    </div>
    @if($accommodations->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">⌂</div>
            <h3>No accommodations yet</h3>
            <p>Add the first room or suite for the website.</p>
            <a class="button" href="{{ route('admin.accommodations.create') }}">Add accommodation</a>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Accommodation</th><th>Status</th><th>Order</th><th>Updated</th><th><span class="sr-only">Actions</span></th></tr></thead>
                <tbody>
                @foreach($accommodations as $accommodation)
                    <tr>
                        <td>
                            <div class="accommodation-table-title">
                                @if($accommodation->imageUrl())<img class="accommodation-thumb" src="{{ $accommodation->imageUrl() }}" alt="">@endif
                                <div><span class="table-title">{{ $accommodation->name }}</span><small>{{ $accommodation->category }} · /{{ $accommodation->slug }}</small></div>
                            </div>
                        </td>
                        <td><span class="badge {{ $accommodation->is_published ? 'badge-published' : 'badge-draft' }}">{{ $accommodation->is_published ? 'Published' : 'Draft' }}</span></td>
                        <td>{{ $accommodation->sort_order }}</td>
                        <td>{{ $accommodation->updated_at->diffForHumans() }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.accommodations.edit', $accommodation) }}">Edit</a>
                                @if($accommodation->is_published)<a href="{{ route('accommodations.show', $accommodation->slug) }}" target="_blank" rel="noopener">View ↗</a>@endif
                                <form method="post" action="{{ route('admin.accommodations.destroy', $accommodation) }}" onsubmit="return confirm('Delete {{ addslashes($accommodation->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="text-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($accommodations->hasPages())
            <div class="pagination">{{ $accommodations->links() }}</div>
        @endif
    @endif
</section>
@endsection
