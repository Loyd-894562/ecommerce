<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'recipient_name',
        'phone',
        'address_line',
        'barangay',
        'city',
        'province',
        'postal_code',
        'notes',
        'subtotal',
        'shipping_fee',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    // The customer who placed the order.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Products purchased in this order.
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // An order may have multiple payment attempts.
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // An order can have zero or one shipment.
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    // Calculate the order total from its components.
    public function calculateTotal(): float
    {
        return (float) $this->subtotal
            + (float) $this->shipping_fee;
    }
}
