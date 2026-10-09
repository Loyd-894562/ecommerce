<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // The product may be deleted, so this relationship is optional.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Calculate the total for this order item.
    public function getLineTotalAttribute(): float
    {
        return (float) $this->unit_price * $this->quantity;
    }
}
