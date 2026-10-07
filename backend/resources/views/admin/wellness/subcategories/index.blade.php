@extends('layouts.admin')
@section('title', 'Wellness subcategories')
@section('content')
<div class="page-heading"><div><p class="eyebrow">WELLNESS PROGRAMS</p><h1>Subcategories</h1><p class="muted">Subcategories form the middle level between a category and its offerings.</p></div><a class="button" href="{{ route('admin.wellness-subcategories.create', $categoryId ? ['category' => $categoryId] : []) }}">Add subcategory →</a></div>
<section class="panel">
<div class="panel-heading"><div><h2>Wellness subcategories</h2><p class="muted">{{ $subcategories->count() }} {{ Str::plural('subcategory', $subcategories->count()) }}</p></div><a href="{{ route('admin.wellness-categories.index') }}">Manage categories →</a></div>
<form class="filter-bar" method="get"><select name="category" aria-label="Filter by parent category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name }}</option>@endforeach</select><button class="button button-outline" type="submit">Filter</button></form>
@if($subcategories->isEmpty())
<div class="empty-state"><span class="empty-icon">◇</span><h3>No subcategories found</h3><p>Create a subcategory before adding offerings.</p><a class="button" href="{{ route('admin.wellness-subcategories.create', $categoryId ? ['category' => $categoryId] : []) }}">Add subcategory</a></div>
@else
<div class="table-wrap"><table><thead><tr><th>Subcategory</th><th>Parent category</th><th>Offerings</th><th>Status</th><th>Order</th><th>Actions</th></tr></thead><tbody>
@foreach($subcategories as $subcategory)
<tr><td><span class="table-title">{{ $subcategory->name }}</span><small>/{{ $subcategory->slug }}@if($subcategory->nav_description) · {{ $subcategory->nav_description }}@endif</small></td><td>{{ $subcategory->category->name }}</td><td>{{ $subcategory->offerings_count }}</td><td><span class="badge {{ $subcategory->is_published ? 'badge-published' : 'badge-draft' }}">{{ $subcategory->is_published ? 'Published' : 'Draft' }}</span></td><td>{{ $subcategory->sort_order }}</td><td><div class="table-actions"><a href="{{ route('admin.wellness-offerings.index', ['subcategory' => $subcategory->id]) }}">Offerings</a><a href="{{ route('admin.wellness-subcategories.edit', $subcategory) }}">Edit</a><form method="post" action="{{ route('admin.wellness-subcategories.destroy', $subcategory) }}" onsubmit="return confirm('Delete this empty subcategory?')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete</button></form></div></td></tr>
@endforeach
</tbody></table></div>
@endif
</section>
@endsection
