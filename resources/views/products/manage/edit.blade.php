@extends('layouts.app')

@section('title', 'Edit product')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8"><p><a class="text-decoration-none" href="{{ route('manage.products.index') }}">← Product management</a></p><div class="card"><div class="card-body p-4"><h1 class="h3 mb-4">Edit {{ $product->name }}</h1><form method="POST" action="{{ route('manage.products.update', $product) }}">@csrf @method('PUT') @include('products.manage._form')<button class="btn btn-primary mt-3" type="submit">Save changes</button></form></div></div></div></div>
@endsection
