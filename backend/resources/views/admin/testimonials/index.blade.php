@extends('layouts.admin')
@section('title', 'Testimonials')
@section('content')
<div class="page-heading"><div><p class="eyebrow">CHRONICLES</p><h1>Testimonials</h1><p class="muted">Manage guest reflections shown on the homepage and Testimonials page.</p></div><a class="button" href="{{ route('admin.testimonials.create') }}">+ Add testimonial</a></div>
<section class="panel">
    <div class="panel-heading"><div><h2>Guest reflections</h2><p class="muted">{{ $testimonials->total() }} testimonials</p></div><a href="{{ route('testimonials') }}" target="_blank" rel="noopener">View website ↗</a></div>
    @if($testimonials->isEmpty())
        <div class="empty-state"><div class="empty-icon">“</div><h3>No testimonials yet</h3><a class="button" href="{{ route('admin.testimonials.create') }}">Add testimonial</a></div>
    @else
        <div class="table-wrap"><table><thead><tr><th>Guest</th><th>Title</th><th>Rating</th><th>Status</th><th>Order</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody>
        @foreach($testimonials as $testimonial)
            <tr><td><span class="table-title">{{ $testimonial->guest_name }}</span><small>{{ $testimonial->guest_location ?: 'Location not set' }}</small></td><td>{{ $testimonial->title }}</td><td>{{ str_repeat('★', $testimonial->rating) }}</td><td><span class="badge {{ $testimonial->is_published ? 'badge-published' : 'badge-draft' }}">{{ $testimonial->is_published ? 'Published' : 'Draft' }}</span></td><td>{{ $testimonial->sort_order }}</td><td><div class="table-actions"><a href="{{ route('admin.testimonials.edit', $testimonial) }}">Edit</a><form method="post" action="{{ route('admin.testimonials.destroy', $testimonial) }}" onsubmit="return confirm('Delete this testimonial?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete</button></form></div></td></tr>
        @endforeach
        </tbody></table></div>
        @if($testimonials->hasPages())<div class="pagination">{{ $testimonials->links() }}</div>@endif
    @endif
</section>
@endsection
