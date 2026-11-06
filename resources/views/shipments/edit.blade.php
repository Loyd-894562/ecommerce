@extends('layouts.app')

@section('title', 'Assign shipment')

@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><p><a class="text-decoration-none" href="{{ route('manage.shipments.index') }}">← Shipments</a></p><div class="card"><div class="card-body p-4"><p class="text-primary fw-semibold mb-1">{{ $shipment->order->order_number }}</p><h1 class="h3 mb-2">Assign delivery</h1><p class="text-secondary mb-4">Deliver to {{ $shipment->order->recipient_name }} · {{ $shipment->order->city }}</p><form method="POST" action="{{ route('manage.shipments.update', $shipment) }}">@csrf @method('PUT')<div class="mb-3"><label class="form-label" for="courier_id">Courier</label><select class="form-select" id="courier_id" name="courier_id"><option value="">Unassigned</option>@foreach ($couriers as $courier)<option value="{{ $courier->id }}" @selected(old('courier_id', $shipment->courier_id) == $courier->id)>{{ $courier->name }} — {{ $courier->email }}</option>@endforeach</select></div><div class="mb-4"><label class="form-label" for="tracking_number">Tracking number</label><input class="form-control" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $shipment->tracking_number) }}"></div><button class="btn btn-primary" type="submit">Save assignment</button></form></div></div></div></div>
@endsection
