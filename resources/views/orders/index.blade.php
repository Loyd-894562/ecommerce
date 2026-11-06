@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="mb-4"><p class="text-primary fw-semibold mb-1">ORDERS</p><h1 class="h2 mb-0">{{ $user->hasRole('customer') ? 'Your orders' : ($user->hasRole('seller') ? 'Orders to fulfill' : 'All orders') }}</h1></div>
<div class="card overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Order</th>@if (! $user->hasRole('customer'))<th>Customer</th>@endif<th>Date</th><th>Status</th><th>Total</th><th></th></tr></thead><tbody>
@forelse ($orders as $order)
    <tr><td class="fw-semibold">{{ $order->order_number }}</td>@if (! $user->hasRole('customer'))<td>{{ $order->user->name }}</td>@endif<td>{{ $order->created_at->format('M j, Y') }}</td><td><span class="badge text-bg-light border">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span></td><td>₱{{ number_format((float) $order->total_amount, 2) }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('orders.show', $order) }}">View order</a></td></tr>
@empty
    <tr><td class="text-center text-secondary py-5" colspan="{{ $user->hasRole('customer') ? 5 : 6 }}">No orders yet.</td></tr>
@endforelse
</tbody></table></div></div><div class="mt-4">{{ $orders->links('pagination::bootstrap-5') }}</div>
@endsection
