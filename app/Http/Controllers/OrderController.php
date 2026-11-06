<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $orders = Order::query()
            ->with(['user', 'shipment', 'items'])
            ->when($user->hasRole('customer'), fn ($query) => $query->where('user_id', $user->id))
            ->when($user->hasRole('seller'), fn ($query) => $query->whereHas(
                'items.product',
                fn ($productQuery) => $productQuery->where('seller_id', $user->id),
            ))
            ->latest('id')
            ->paginate(15);

        return view('orders.index', compact('orders', 'user'));
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorizeOrderAccess($request->user(), $order);
        $order->load(['user', 'items.product', 'payments', 'shipment.courier']);

        return view('orders.show', compact('order'));
    }

    public function fulfill(Request $request, Order $order): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(
            $user->hasRole('admin') || ($user->hasRole('seller') && $this->sellerOwnsOrderItem($user, $order)),
            403,
        );
        abort_unless($order->status === 'processing', 422, 'Only paid orders can be prepared for dispatch.');

        $order->update(['status' => 'ready_for_shipment']);
        Shipment::firstOrCreate(
            ['order_id' => $order->id],
            ['status' => 'pending'],
        );

        return redirect()->route('orders.show', $order)->with('status', 'Order marked ready for shipment.');
    }

    private function authorizeOrderAccess(User $user, Order $order): void
    {
        $isAllowed = $user->hasRole('admin')
            || ($user->hasRole('customer') && $order->user_id === $user->id)
            || ($user->hasRole('seller') && $this->sellerOwnsOrderItem($user, $order));

        abort_unless($isAllowed, 403);
    }

    private function sellerOwnsOrderItem(User $seller, Order $order): bool
    {
        return $order->items()
            ->whereHas('product', fn ($query) => $query->where('seller_id', $seller->id))
            ->exists();
    }
}
