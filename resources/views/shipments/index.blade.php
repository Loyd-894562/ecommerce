@extends('layouts.app')

@section('title', 'Manage shipments')

@section('content')
<div class="mb-4"><p class="text-primary fw-semibold mb-1">DELIVERY</p><h1 class="h2 mb-0">Shipments</h1></div>
<div class="card overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Order</th><th>Recipient</th><th>Tracking</th><th>Courier</th><th>Status</th><th></th></tr></thead><tbody>@forelse ($shipments as $shipment)<tr><td><a href="{{ route('orders.show', $shipment->order) }}">{{ $shipment->order->order_number }}</a></td><td>{{ $shipment->order->recipient_name }}</td><td>{{ $shipment->tracking_number ?: '—' }}</td><td>{{ $shipment->courier?->name ?? 'Unassigned' }}</td><td>{{ str_replace('_', ' ', ucfirst($shipment->status)) }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('manage.shipments.edit', $shipment) }}">Assign</a></td></tr>@empty<tr><td colspan="6" class="text-center text-secondary py-5">No shipments are ready yet. Sellers create shipments when they prepare paid orders.</td></tr>@endforelse</tbody></table></div></div><div class="mt-4">{{ $shipments->links('pagination::bootstrap-5') }}</div>
@endsection
