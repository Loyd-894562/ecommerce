<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLES = ['customer', 'seller', 'admin', 'courier'];

    public function index(): View
    {
        $users = User::query()->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', ['roles' => self::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('manage.users.index')->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', ['roles' => self::ROLES, 'user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
        ]);

        if (filled($validated['password'] ?? null)) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('manage.users.index')->with('status', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 422, 'You cannot delete your own account here.');
        abort_if(
            $user->products()->exists() || $user->orders()->exists(),
            422,
            'This user has products or orders and cannot be deleted.',
        );

        $user->delete();

        return redirect()->route('manage.users.index')->with('status', 'User deleted.');
    }
}
