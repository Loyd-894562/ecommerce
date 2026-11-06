@extends('layouts.app')

@section('title', 'Create account')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm"><div class="card-body p-4 p-lg-5">
            <h1 class="h3 mb-2">Create your account</h1>
            <p class="text-secondary mb-4">New accounts start as customers. An administrator can assign staff roles.</p>
            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="mb-3"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required></div>
                <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required></div>
                <div class="mb-3"><label class="form-label" for="phone">Phone <span class="text-secondary">(optional)</span></label><input class="form-control" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel"></div>
                <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" autocomplete="new-password" required></div>
                <div class="mb-4"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
                <button class="btn btn-primary w-100" type="submit">Create account</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
