<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ShipmentController extends Controller
{
    public function index(): View
    {
        $shipments = Shipment::query()
            ->with(['order.user', 'courier'])
            ->latest('id')
            ->paginate(20);

        return view('shipments.index', compact('shipments'));
    }

    public function edit(Shipment $shipment): View
    {
        $shipment->load('order.user');
        $couriers = User::query()->where('role', 'courier')->orderBy('name')->get();

        return view('shipments.edit', compact('couriers', 'shipment'));
    }

    public function update(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'courier_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'courier'),
            ],
            'tracking_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('shipments', 'tracking_number')->ignore($shipment),
            ],
        ]);

        $shipment->update($validated);

        return redirect()->route('manage.shipments.index')->with('status', 'Shipment assignment updated.');
    }

    public function courierIndex(Request $request): View
    {
        $shipments = $request->user()->shipments()
            ->with('order.user')
            ->latest('id')
            ->paginate(20);

        return view('shipments.courier-index', compact('shipments'));
    }

    public function courierUpdate(Request $request, Shipment $shipment): RedirectResponse
    {
        abort_unless($shipment->courier_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['in_transit', 'delivered'])],
        ]);

        if ($validated['status'] === 'delivered' && $shipment->status !== 'in_transit') {
            throw ValidationException::withMessages([
                'status' => 'Mark the shipment in transit before marking it delivered.',
            ]);
        }

        if ($validated['status'] === 'in_transit' && $shipment->status === 'pending') {
            $shipment->update([
                'status' => 'in_transit',
                'shipped_at' => now(),
            ]);
            $shipment->order()->update(['status' => 'in_transit']);
        } elseif ($validated['status'] === 'delivered') {
            $shipment->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);
            $shipment->order()->update(['status' => 'delivered']);
        }

        return redirect()->route('courier.shipments.index')->with('status', 'Delivery status updated.');
    }
}
