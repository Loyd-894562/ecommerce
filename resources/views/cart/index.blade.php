@extends('layouts.app')

@section('title', 'Your cart')

@section('content')
<div class="mb-4"><p class="text-primary fw-semibold mb-1">YOUR SHOPPING</p><h1 class="h2 mb-0">Shopping cart</h1></div>
@if ($cart->items->isEmpty())
    <div class="card card-body text-center py-5"><h2 class="h4">Your cart is empty</h2><p class="text-secondary">Browse products and add something you like.</p><a class="btn btn-primary align-self-center" href="{{ route('products.index') }}">Browse products</a></div>
@else
    <div class="row g-4"><div class="col-lg-8"><div class="card overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr></thead><tbody>
        @foreach ($cart->items as $item)
            <tr><td><a class="text-decoration-none fw-semibold" href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a></td><td>₱{{ number_format((float) $item->product->price, 2) }}</td><td><form class="d-flex gap-2" method="POST" action="{{ route('cart.items.update', $item) }}">@csrf @method('PATCH')<input class="form-control form-control-sm" style="max-width: 90px" name="quantity" type="number" min="1" max="100" value="{{ $item->quantity }}" aria-label="Quantity"><button class="btn btn-sm btn-outline-secondary" type="submit">Update</button></form></td><td class="fw-semibold">₱{{ number_format((float) $item->product->price * $item->quantity, 2) }}</td><td class="text-end"><form method="POST" action="{{ route('cart.items.destroy', $item) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger" type="submit">Remove</button></form></td></tr>
        @endforeach
    </tbody></table></div></div></div><div class="col-lg-4"><div class="card card-body"><h2 class="h5">Order summary</h2><div class="d-flex justify-content-between border-bottom py-3"><span>Subtotal</span><strong>₱{{ number_format($cart->calculateTotal(), 2) }}</strong></div><p class="small text-secondary mt-3">Shipping is currently free. Stock is checked again at checkout.</p><a class="btn btn-primary w-100" href="{{ route('checkout.create') }}">Continue to checkout</a></div></div></div>
@endif
@endsection
