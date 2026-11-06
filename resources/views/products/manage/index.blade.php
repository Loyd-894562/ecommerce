@extends('layouts.app')

@section('title', 'Manage products')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><p class="text-primary fw-semibold mb-1">CATALOG</p><h1 class="h2 mb-0">Manage products</h1></div><a class="btn btn-primary" href="{{ route('manage.products.create') }}">Add product</a></div>
<div class="card overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
@forelse ($products as $product)
    <tr><td><a class="fw-semibold text-decoration-none" href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td><td>{{ $product->category?->name ?? '—' }}</td><td>₱{{ number_format((float) $product->price, 2) }}</td><td>{{ $product->stock_quantity }}</td><td><span class="badge {{ $product->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($product->status) }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('manage.products.edit', $product) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('manage.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form></td></tr>
@empty
    <tr><td class="text-center text-secondary py-5" colspan="6">No listings yet. Add your first product to get started.</td></tr>
@endforelse
</tbody></table></div></div>
<div class="mt-4">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
