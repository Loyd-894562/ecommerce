<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    // The cart belongs to one customer.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // A cart has many cart items.
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Products contained in this cart.
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cart_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // Calculate the current cart subtotal.
    public function calculateTotal(): float
    {
        return (float) $this->items->sum(function ($item) {
            return (float) $item->product->price * $item->quantity;
        });
    }
}
