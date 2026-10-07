@extends('layouts.admin')
@section('title', 'Wellness offerings')
@section('content')
<div class="page-heading"><div><p class="eyebrow">WELLNESS PROGRAMS</p><h1>Offerings</h1><p class="muted">Offerings are the final level beneath a category and subcategory.</p></div><a class="button" href="{{ route('admin.wellness-offerings.create', $subcategoryId ? ['subcategory' => $subcategoryId] : []) }}">Add offering →</a></div>
<section class="panel">
<div class="panel-heading"><div><h2>All offerings</h2><p class="muted">{{ $offerings->total() }} {{ Str::plural('item', $offerings->total()) }}</p></div><a href="{{ route('admin.wellness-subcategories.index') }}">Manage subcategories →</a></div>
<form class="filter-bar" method="get"><select name="category" aria-label="Filter by category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>@endforeach</select><select name="subcategory" aria-label="Filter by subcategory"><option value="">All subcategories</option>@foreach($subcategories->groupBy('wellness_category_id') as $group)<optgroup label="{{ $group->first()->category->name }}">@foreach($group as $subcategory)<option value="{{ $subcategory->id }}" @selected($subcategoryId === $subcategory->id)>{{ $subcategory->name }}</option>@endforeach</optgroup>@endforeach</select><button class="button button-outline" type="submit">Filter</button></form>
@if($offerings->isEmpty())
<div class="empty-state"><span class="empty-icon">✦</span><h3>No offerings found</h3><p>Create a category and subcategory first, then add an offering.</p><a class="button" href="{{ route('admin.wellness-offerings.create', $subcategoryId ? ['subcategory' => $subcategoryId] : []) }}">Add offering</a></div>
@else
<div class="table-wrap"><table><thead><tr><th>Offering</th><th>Category</th><th>Subcategory</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead><tbody>
@foreach($offerings as $offering)
<tr><td><span class="table-title">{{ $offering->name }}</span><small>/{{ $offering->slug }} · {{ $offering->eyebrow }}</small></td><td>{{ $offering->category->name }}</td><td>{{ $offering->subcategory?->name ?? 'Unassigned' }}</td><td><span class="badge {{ $offering->is_published ? 'badge-published' : 'badge-draft' }}">{{ $offering->is_published ? 'Published' : 'Draft' }}</span></td><td>{{ $offering->sort_order }}</td><td><div class="table-actions">@if($offering->is_published && $offering->category->is_published && $offering->subcategory?->is_published)<a href="{{ $offering->publicUrl() }}" target="_blank" rel="noopener">View ↗</a>@endif<a href="{{ route('admin.wellness-offerings.edit', $offering) }}">Edit</a><form method="post" action="{{ route('admin.wellness-offerings.destroy', $offering) }}" onsubmit="return confirm('Delete this offering? This cannot be undone.')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete</button></form></div></td></tr>
@endforeach
</tbody></table></div>
@if($offerings->hasPages())<div class="pagination">{{ $offerings->links() }}</div>@endif
@endif
</section>
@endsection
