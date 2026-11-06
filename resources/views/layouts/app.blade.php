<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Shop') · {{ config('app.name', 'Shop') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f6f7fb; color: #182230; }
        .navbar-brand { font-weight: 700; letter-spacing: -.03em; }
        .page-shell { min-height: calc(100vh - 72px); }
        .card { border: 1px solid #e7eaf0; border-radius: 1rem; }
        .product-image { height: 220px; object-fit: cover; background: #edf0f5; }
        .muted-label { color: #697586; font-size: .875rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name', 'Shop') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                @auth
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    @if (auth()->user()->hasRole('customer'))
                        <a class="nav-link" href="{{ route('cart.index') }}">Cart</a>
                    @endif
                    @if (in_array(auth()->user()->role, ['customer', 'seller', 'admin'], true))
                        <a class="nav-link" href="{{ route('orders.index') }}">Orders</a>
                    @endif
                    @if (in_array(auth()->user()->role, ['seller', 'admin'], true))
                        <a class="nav-link" href="{{ route('manage.products.index') }}">Manage products</a>
                    @endif
                    @if (auth()->user()->hasRole('admin'))
                        <a class="nav-link" href="{{ route('manage.categories.index') }}">Categories</a>
                        <a class="nav-link" href="{{ route('manage.users.index') }}">Users</a>
                        <a class="nav-link" href="{{ route('manage.shipments.index') }}">Shipments</a>
                    @endif
                    @if (auth()->user()->hasRole('courier'))
                        <a class="nav-link" href="{{ route('courier.shipments.index') }}">My deliveries</a>
                    @endif
                @endauth
            </div>
            <div class="navbar-nav align-items-lg-center gap-lg-2">
                @guest
                    <a class="nav-link" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Create account</a>
                @else
                    <span class="nav-link text-secondary">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Log out</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>

<main class="container page-shell py-4 py-lg-5">
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please review the form.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
