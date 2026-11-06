@extends('layouts.app')

@section('title', 'Edit user')

@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><p><a class="text-decoration-none" href="{{ route('manage.users.index') }}">← Users</a></p><div class="card"><div class="card-body p-4"><h1 class="h3 mb-4">Edit {{ $user->name }}</h1><form method="POST" action="{{ route('manage.users.update', $user) }}">@csrf @method('PUT') @include('users._form')<button class="btn btn-primary mt-3" type="submit">Save changes</button></form></div></div></div></div>
@endsection
