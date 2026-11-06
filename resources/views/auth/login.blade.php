@extends('layouts.app')

@section('title', 'Log in')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm"><div class="card-body p-4 p-lg-5">
            <h1 class="h3 mb-2">Welcome back</h1>
            <p class="text-secondary mb-4">Log in to continue shopping or manage your store.</p>
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button class="btn btn-primary w-100" type="submit">Log in</button>
            </form>
            <p class="text-secondary mt-4 mb-0">New here? <a href="{{ route('register') }}">Create a customer account</a>.</p>
        </div></div>
    </div>
</div>
@endsection
