<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->user()->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Add a product before checking out.');
        }

        return view('checkout.create', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address_line' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $order = DB::transaction(function () use ($user, $validated): Order {
            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();
            $cartItems = $cart->items()->with('product')->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
            }

            $products = Product::query()
                ->whereKey($cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0.0;
            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                if (! $product || ! $product->isAvailable($cartItem->quantity)) {
                    throw ValidationException::withMessages([
                        'cart' => 'One or more cart items are no longer available in the requested quantity.',
                    ]);
                }

                $subtotal += (float) $product->price * $cartItem->quantity;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-'.Str::upper(Str::random(10)),
                'status' => 'pending_payment',
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'address_line' => $validated['address_line'],
                'barangay' => $validated['barangay'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => 0,
                'total_amount' => $subtotal,
            ]);

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            return $order;
        });

        return redirect()->route('payments.create', $order)->with('status', 'Review your order and complete the demo payment.');
    }
}
