@extends('layouts.app')

@section('title', 'Manage users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><p class="text-primary fw-semibold mb-1">ADMINISTRATION</p><h1 class="h2 mb-0">Users</h1></div><a class="btn btn-primary" href="{{ route('manage.users.create') }}">Add user</a></div>
<div class="card overflow-hidden"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th class="text-end">Actions</th></tr></thead><tbody>@forelse ($users as $user)<tr><td class="fw-semibold">{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->phone ?: '—' }}</td><td><span class="badge text-bg-light border">{{ ucfirst($user->role) }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('manage.users.edit', $user) }}">Edit</a>@if ($user->id !== auth()->id()) <form class="d-inline" method="POST" action="{{ route('manage.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form>@endif</td></tr>@empty<tr><td colspan="5" class="text-center text-secondary py-5">No users found.</td></tr>@endforelse</tbody></table></div></div><div class="mt-4">{{ $users->links('pagination::bootstrap-5') }}</div>
@endsection
