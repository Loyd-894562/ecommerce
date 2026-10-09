<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // A seller can list many products.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    // A customer has one cart.
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    // A customer can place many orders.
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // A courier can be assigned many shipments.
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'courier_id');
    }

    // A customer can write many reviews.
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Helper method for checking a user's role.
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
