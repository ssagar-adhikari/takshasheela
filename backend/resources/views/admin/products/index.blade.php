@extends('layouts.admin')
@section('title', 'Products')
@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">APOTHECARY</p>
        <h1>Products</h1>
        <p class="muted">Manage the catalogue cards and the content shown on every product detail page.</p>
    </div>
    <a class="button" href="{{ route('admin.products.create') }}">+ Add product</a>
</div>
<section class="panel">
    <div class="panel-heading">
        <div><h2>Product catalogue</h2><p class="muted">{{ $products->total() }} {{ $products->total() === 1 ? 'product' : 'products' }}</p></div>
        <a href="{{ route('products.index') }}" target="_blank" rel="noopener">View listing page ↗</a>
    </div>
    @if($products->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">◈</div>
            <h3>No products yet</h3>
            <p>Add the first product for the public catalogue.</p>
            <a class="button" href="{{ route('admin.products.create') }}">Add product</a>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Product</th><th>Price</th><th>Status</th><th>Order</th><th>Updated</th><th><span class="sr-only">Actions</span></th></tr></thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <div class="product-table-title">
                                @if($product->imageUrl())<img class="product-thumb" src="{{ $product->imageUrl() }}" alt="">@endif
                                <div><span class="table-title">{{ $product->name }}</span><small>{{ $product->category }} · {{ $product->size }} · /{{ $product->slug }}</small></div>
                            </div>
                        </td>
                        <td>{{ $product->price }}</td>
                        <td><span class="badge {{ $product->is_published ? 'badge-published' : 'badge-draft' }}">{{ $product->is_published ? 'Published' : 'Draft' }}</span></td>
                        <td>{{ $product->sort_order }}</td>
                        <td>{{ $product->updated_at->diffForHumans() }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                                @if($product->is_published)<a href="{{ route('products.show', $product->slug) }}" target="_blank" rel="noopener">View ↗</a>@endif
                                <form method="post" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')">
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
        @if($products->hasPages())
            <div class="pagination">{{ $products->links() }}</div>
        @endif
    @endif
</section>
@endsection
