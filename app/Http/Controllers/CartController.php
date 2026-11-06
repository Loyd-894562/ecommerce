<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->cartFor($request->user());
        $cart->load('items.product');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $cart = $this->cartFor($request->user());

        DB::transaction(function () use ($cart, $validated): void {
            $product = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);
            $cart = Cart::query()->whereKey($cart->id)->lockForUpdate()->firstOrFail();
            $cartItem = $cart->items()->where('product_id', $product->id)->first();
            $quantity = (int) $validated['quantity'] + ($cartItem?->quantity ?? 0);

            if (! $product->isAvailable($quantity)) {
                throw ValidationException::withMessages([
                    'quantity' => 'The requested quantity is not available in stock.',
                ]);
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $quantity]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }
        });

        return redirect()->route('cart.index')->with('status', 'Product added to your cart.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        abort_unless($cartItem->cart()->where('user_id', $request->user()->id)->exists(), 404);

        DB::transaction(function () use ($cartItem, $validated): void {
            $product = Product::query()->lockForUpdate()->findOrFail($cartItem->product_id);
            $quantity = (int) $validated['quantity'];

            if (! $product->isAvailable($quantity)) {
                throw ValidationException::withMessages([
                    'quantity' => 'The requested quantity is not available in stock.',
                ]);
            }

            $cartItem->update(['quantity' => $quantity]);
        });

        return redirect()->route('cart.index')->with('status', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart()->where('user_id', $request->user()->id)->exists(), 404);
        $cartItem->delete();

        return redirect()->route('cart.index')->with('status', 'Product removed from your cart.');
    }

    private function cartFor(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }
}
