@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><p class="text-primary fw-semibold mb-1">CATALOG</p><h1 class="h2 mb-0">Categories</h1></div><a class="btn btn-primary" href="{{ route('manage.categories.create') }}">Add category</a></div>
<div class="card overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Name</th><th>Description</th><th>Products</th><th class="text-end">Actions</th></tr></thead><tbody>@forelse ($categories as $category)<tr><td class="fw-semibold">{{ $category->name }}</td><td>{{ $category->description ?: '—' }}</td><td>{{ $category->products_count }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('manage.categories.edit', $category) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('manage.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Products will become uncategorized.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form></td></tr>@empty<tr><td class="text-center text-secondary py-5" colspan="4">No categories created yet.</td></tr>@endforelse</tbody></table></div></div><div class="mt-4">{{ $categories->links('pagination::bootstrap-5') }}</div>
@endsection
