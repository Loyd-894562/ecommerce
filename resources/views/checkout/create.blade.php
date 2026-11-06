@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="row g-4"><div class="col-lg-7"><p class="text-primary fw-semibold mb-1">CHECKOUT</p><h1 class="h2 mb-4">Delivery details</h1><div class="card"><div class="card-body p-4">
    <form method="POST" action="{{ route('checkout.store') }}">@csrf
        <div class="mb-3"><label class="form-label" for="recipient_name">Recipient name</label><input class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required></div>
        <div class="mb-3"><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required></div>
        <div class="mb-3"><label class="form-label" for="address_line">Street address / house number</label><input class="form-control" id="address_line" name="address_line" value="{{ old('address_line') }}" required></div>
        <div class="row g-3"><div class="col-md-6"><label class="form-label" for="barangay">Barangay</label><input class="form-control" id="barangay" name="barangay" value="{{ old('barangay') }}" required></div><div class="col-md-6"><label class="form-label" for="city">City</label><input class="form-control" id="city" name="city" value="{{ old('city') }}" required></div><div class="col-md-8"><label class="form-label" for="province">Province</label><input class="form-control" id="province" name="province" value="{{ old('province') }}" required></div><div class="col-md-4"><label class="form-label" for="postal_code">Postal code</label><input class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}"></div></div>
        <div class="mt-3 mb-4"><label class="form-label" for="notes">Delivery notes <span class="text-secondary">(optional)</span></label><textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea></div>
        <button class="btn btn-primary" type="submit">Review order and continue to payment</button>
    </form>
</div></div></div><div class="col-lg-5"><div class="card card-body"><h2 class="h5">Items in your order</h2>@foreach ($cart->items as $item)<div class="d-flex justify-content-between border-bottom py-2"><span>{{ $item->product->name }} × {{ $item->quantity }}</span><strong>₱{{ number_format((float) $item->product->price * $item->quantity, 2) }}</strong></div>@endforeach<div class="d-flex justify-content-between pt-3 fs-5"><span>Total</span><strong>₱{{ number_format($cart->calculateTotal(), 2) }}</strong></div></div></div></div>
@endsection
