@extends('layouts.app')

@section('title', 'My deliveries')

@section('content')
<div class="mb-4"><p class="text-primary fw-semibold mb-1">COURIER</p><h1 class="h2 mb-0">My delivery queue</h1></div>
<div class="row g-3">@forelse ($shipments as $shipment)<div class="col-lg-6"><article class="card h-100"><div class="card-body"><div class="d-flex justify-content-between gap-3"><div><p class="muted-label mb-1">{{ $shipment->order->order_number }}</p><h2 class="h5">{{ $shipment->order->recipient_name }}</h2></div><span class="badge text-bg-light border align-self-start">{{ str_replace('_', ' ', ucfirst($shipment->status)) }}</span></div><p class="mb-1">{{ $shipment->order->address_line }}</p><p>{{ $shipment->order->barangay }}, {{ $shipment->order->city }}, {{ $shipment->order->province }}</p><p class="small text-secondary">Phone: {{ $shipment->order->phone }} · Tracking: {{ $shipment->tracking_number ?: 'Not assigned' }}</p>
    @if ($shipment->status === 'pending')<form method="POST" action="{{ route('courier.shipments.update', $shipment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="in_transit"><button class="btn btn-primary" type="submit">Mark in transit</button></form>@elseif ($shipment->status === 'in_transit')<form method="POST" action="{{ route('courier.shipments.update', $shipment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="delivered"><button class="btn btn-success" type="submit">Mark delivered</button></form>@else<p class="text-success fw-semibold mb-0">Delivered {{ $shipment->delivered_at?->format('M j, Y g:i A') }}</p>@endif
    </div></article></div>@empty<div class="col-12"><div class="card card-body text-center py-5"><h2 class="h5">No deliveries assigned</h2><p class="text-secondary mb-0">Assigned deliveries will show up here.</p></div></div>@endforelse</div><div class="mt-4">{{ $shipments->links('pagination::bootstrap-5') }}</div>
@endsection
