<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $hasDeliveredOrder = $user->orders()
            ->where('status', 'delivered')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();

        abort_unless($hasDeliveredOrder, 403, 'You can review a product after it has been delivered.');

        Review::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            $validated,
        );

        return redirect()->route('products.show', $product)->with('status', 'Your review has been saved.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        abort_unless($review->user_id === $request->user()->id, 403);
        $product = $review->product;
        $review->delete();

        return redirect()->route('products.show', $product)->with('status', 'Your review has been removed.');
    }
}
