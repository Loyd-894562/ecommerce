<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    // Fields that can be assigned when creating or updating a product.
    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock_quantity',
        'image_path',
        'status',
    ];

    // Automatically convert database values to appropriate types.
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
        ];
    }

    // A product belongs to one seller.
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // A product belongs to one category.
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // A product can appear in many cart items.
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // A product can appear in many order items.
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // A product can receive many reviews.
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // A product can belong to many carts through cart_items.
    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // Check whether sufficient stock is available.
    public function isAvailable(int $quantity = 1): bool
    {
        return $this->status === 'active'
            && $quantity > 0
            && $this->stock_quantity >= $quantity;
    }
}
