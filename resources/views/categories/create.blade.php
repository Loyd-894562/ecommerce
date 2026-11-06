@extends('layouts.app')

@section('title', 'Add category')

@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><p><a class="text-decoration-none" href="{{ route('manage.categories.index') }}">← Categories</a></p><div class="card"><div class="card-body p-4"><h1 class="h3 mb-4">Add category</h1><form method="POST" action="{{ route('manage.categories.store') }}">@csrf @include('categories._form', ['category' => null])<button class="btn btn-primary mt-3" type="submit">Create category</button></form></div></div></div></div>
@endsection
