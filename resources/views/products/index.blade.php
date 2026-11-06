@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><p class="text-primary fw-semibold mb-1">ONLINE SHOPPING</p><h1 class="display-6 fw-bold mb-1">Find something you love</h1><p class="text-secondary mb-0">Browse the latest products from our sellers.</p></div>
    @auth
        @if (in_array(auth()->user()->role, ['seller', 'admin'], true))
            <a class="btn btn-outline-primary" href="{{ route('manage.products.index') }}">Manage listings</a>
        @endif
    @endauth
</div>

<form class="card card-body mb-4" method="GET" action="{{ route('products.index') }}">
    <div class="row g-2 align-items-end">
        <div class="col-md-6"><label class="form-label" for="search">Search products</label><input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Product name"></div>
        <div class="col-md-4"><label class="form-label" for="category">Category</label><select class="form-select" id="category" name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-md-2 d-grid"><button class="btn btn-dark" type="submit">Search</button></div>
    </div>
</form>

<div class="row g-4">
    @forelse ($products as $product)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <article class="card h-100 overflow-hidden shadow-sm">
                @if ($product->image_path)<img class="card-img-top product-image" src="{{ $product->image_path }}" alt="{{ $product->name }}">@else<div class="product-image d-flex align-items-center justify-content-center text-secondary">No image</div>@endif
                <div class="card-body d-flex flex-column">
                    <p class="muted-label mb-1">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                    <h2 class="h5"><a class="text-decoration-none text-dark" href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h2>
                    <p class="text-secondary small">Sold by {{ $product->seller->name }}</p>
                    <div class="mt-auto d-flex justify-content-between align-items-center"><span class="fs-5 fw-bold">₱{{ number_format((float) $product->price, 2) }}</span><span class="small text-secondary">{{ $product->stock_quantity }} in stock</span></div>
                    <a class="btn btn-outline-primary mt-3" href="{{ route('products.show', $product) }}">View product</a>
                </div>
            </article>
        </div>
    @empty
        <div class="col-12"><div class="card card-body text-center py-5"><h2 class="h5">No products found</h2><p class="text-secondary mb-0">Try another search or check back later.</p></div></div>
    @endforelse
</div>
<div class="mt-4">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
