@extends('layouts.app')

@section('title', $product->name)

@section('content')
<p><a href="{{ route('products.index') }}" class="text-decoration-none">← Back to products</a></p>
<div class="row g-4 g-lg-5">
    <div class="col-lg-6">
        <div class="card overflow-hidden shadow-sm">
            @if ($product->image_path)<img class="w-100 product-image" src="{{ $product->image_path }}" alt="{{ $product->name }}">@else<div class="product-image d-flex align-items-center justify-content-center text-secondary">No image</div>@endif
        </div>
    </div>
    <div class="col-lg-6">
        <p class="text-primary fw-semibold mb-2">{{ $product->category?->name ?? 'Uncategorized' }}</p>
        <h1 class="display-6 fw-bold">{{ $product->name }}</h1>
        <p class="text-secondary">Sold by {{ $product->seller->name }}</p>
        <p class="fs-2 fw-bold">₱{{ number_format((float) $product->price, 2) }}</p>
        <p>{{ $product->description ?: 'No description has been added yet.' }}</p>
        <p class="text-secondary">{{ $product->stock_quantity }} available</p>
        @if ($product->status !== 'active')<span class="badge text-bg-secondary mb-3">Inactive listing</span>@endif
        @if ($canManage)<p><a class="btn btn-outline-secondary" href="{{ route('manage.products.edit', $product) }}">Edit listing</a></p>@endif
        @if ($product->status === 'active' && $product->stock_quantity > 0)
            @auth
                @if (auth()->user()->hasRole('customer'))
                    <form class="row g-2" method="POST" action="{{ route('cart.items.store') }}">
                        @csrf<input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="col-4"><label class="form-label" for="quantity">Quantity</label><input class="form-control" id="quantity" name="quantity" type="number" min="1" max="{{ min(100, $product->stock_quantity) }}" value="1" required></div>
                        <div class="col-auto align-self-end"><button class="btn btn-primary" type="submit">Add to cart</button></div>
                    </form>
                @endif
            @else
                <a class="btn btn-primary" href="{{ route('login') }}">Log in to add to cart</a>
            @endauth
        @else
            <button class="btn btn-secondary" type="button" disabled>Currently unavailable</button>
        @endif
    </div>
</div>

<section class="mt-5">
    <h2 class="h3 mb-3">Customer reviews</h2>
    @auth
        @if (auth()->user()->hasRole('customer') && $canReview)
            @php($myReview = $product->reviews->firstWhere('user_id', auth()->id()))
            <div class="card card-body mb-4">
                <h3 class="h6">{{ $myReview ? 'Update your review' : 'Write a review' }}</h3>
                <form method="POST" action="{{ route('reviews.store', $product) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="rating">Rating</label><select class="form-select" id="rating" name="rating" required>@foreach (range(5, 1) as $rating)<option value="{{ $rating }}" @selected(old('rating', $myReview?->rating) == $rating)>{{ $rating }} / 5</option>@endforeach</select></div>
                        <div class="col-md-9"><label class="form-label" for="comment">Comment</label><textarea class="form-control" id="comment" name="comment" rows="2">{{ old('comment', $myReview?->comment) }}</textarea></div>
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">Save review</button>
                </form>
            </div>
        @endif
    @endauth
    <div class="row g-3">
        @forelse ($product->reviews as $review)
            <div class="col-md-6"><article class="card card-body h-100"><div class="d-flex justify-content-between"><strong>{{ $review->user->name }}</strong><span class="text-warning">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span></div><p class="text-secondary mb-0 mt-2">{{ $review->comment ?: 'No comment.' }}</p>@auth @if ($review->user_id === auth()->id())<form class="mt-3" method="POST" action="{{ route('reviews.destroy', $review) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Remove my review</button></form>@endif @endauth</article></div>
        @empty
            <p class="text-secondary">No reviews yet.</p>
        @endforelse
    </div>
</section>
@endsection
