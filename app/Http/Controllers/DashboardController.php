<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $dashboardData = match ($user->role) {
            'seller' => [
                'productCount' => Product::where('seller_id', $user->id)->count(),
                'recentOrders' => Order::query()
                    ->whereHas('items.product', fn ($query) => $query->where('seller_id', $user->id))
                    ->with('user')
                    ->latest('id')
                    ->take(5)
                    ->get(),
            ],
            'admin' => [
                'userCount' => User::count(),
                'productCount' => Product::count(),
                'orderCount' => Order::count(),
            ],
            'courier' => [
                'shipments' => $user->shipments()
                    ->with('order.user')
                    ->latest('id')
                    ->take(5)
                    ->get(),
            ],
            default => [
                'cartItemCount' => $user->cart?->items()->sum('quantity') ?? 0,
                'recentOrders' => $user->orders()->latest('id')->take(5)->get(),
            ],
        };

        return view('dashboard.index', compact('dashboardData', 'user'));
    }
}
