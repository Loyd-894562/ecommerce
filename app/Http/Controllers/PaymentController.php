<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function create(Request $request, Order $order): View|RedirectResponse
    {
        $this->authorizeCustomerOrder($request->user(), $order);

        if ($order->status !== 'pending_payment') {
            return redirect()->route('orders.show', $order);
        }

        $order->load('items');

        return view('payments.create', compact('order'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'method' => ['required', Rule::in(['demo_card', 'demo_wallet', 'demo_declined'])],
        ]);

        /** @var User $user */
        $user = $request->user();

        $paymentSucceeded = DB::transaction(function () use ($order, $user, $validated): bool {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $this->authorizeCustomerOrder($user, $lockedOrder);

            if ($lockedOrder->status !== 'pending_payment') {
                throw ValidationException::withMessages(['payment' => 'This order has already been paid or cancelled.']);
            }

            if ($validated['method'] === 'demo_declined') {
                $lockedOrder->payments()->create([
                    'amount' => $lockedOrder->total_amount,
                    'method' => $validated['method'],
                    'status' => 'failed',
                    'transaction_reference' => 'DEMO-'.Str::upper(Str::random(12)),
                ]);

                return false;
            }

            $items = $lockedOrder->items()->get();
            $products = Product::query()
                ->whereKey($items->pluck('product_id')->filter())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get($item->product_id);
                if (! $product || ! $product->isAvailable($item->quantity)) {
                    throw ValidationException::withMessages([
                        'payment' => 'Stock changed before payment. Please contact the seller before retrying.',
                    ]);
                }
            }

            $lockedOrder->payments()->create([
                'amount' => $lockedOrder->total_amount,
                'method' => $validated['method'],
                'status' => 'paid',
                'transaction_reference' => 'DEMO-'.Str::upper(Str::random(12)),
                'paid_at' => now(),
            ]);

            foreach ($items as $item) {
                $products->get($item->product_id)->decrement('stock_quantity', $item->quantity);

                $cartItem = $user->cart?->items()
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($cartItem) {
                    if ($cartItem->quantity <= $item->quantity) {
                        $cartItem->delete();
                    } else {
                        $cartItem->decrement('quantity', $item->quantity);
                    }
                }
            }

            $lockedOrder->update(['status' => 'processing']);

            return true;
        }, attempts: 3);

        if (! $paymentSucceeded) {
            return redirect()
                ->route('payments.create', $order)
                ->withErrors(['payment' => 'The demo payment was declined. Your order is still awaiting payment.']);
        }

        return redirect()->route('orders.show', $order)->with('status', 'Demo payment recorded and order confirmed.');
    }

    private function authorizeCustomerOrder(User $user, Order $order): void
    {
        abort_unless($user->hasRole('customer') && $order->user_id === $user->id, 403);
    }
}
