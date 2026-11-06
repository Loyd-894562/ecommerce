<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:customer')->group(function (): void {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
        Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
        Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');

        Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');

        Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    Route::middleware('role:customer,seller,admin')->group(function (): void {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    Route::middleware(['role:seller,admin'])->group(function (): void {
        Route::post('/orders/{order}/fulfill', [OrderController::class, 'fulfill'])->name('orders.fulfill');

        Route::prefix('manage')->name('manage.')->group(function (): void {
            Route::get('/products', [ProductController::class, 'manage'])->name('products.index');
            Route::resource('products', ProductController::class)->except(['index', 'show']);
        });
    });

    Route::middleware('role:admin')->prefix('manage')->name('manage.')->group(function (): void {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('shipments', ShipmentController::class)->only(['index', 'edit', 'update']);
    });

    Route::middleware('role:courier')->prefix('courier')->name('courier.')->group(function (): void {
        Route::get('/shipments', [ShipmentController::class, 'courierIndex'])->name('shipments.index');
        Route::patch('/shipments/{shipment}', [ShipmentController::class, 'courierUpdate'])->name('shipments.update');
    });
});
