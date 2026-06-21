<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users   = $query->latest()->paginate(10)->withQueryString();
        $total   = User::count();
        $verified = User::whereNotNull('email_verified_at')->count();
        $byRole  = User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role');

        return view('admin.users.index', compact('users', 'total', 'verified', 'byRole'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role'     => ['required', 'in:admin,editor,viewer'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => $request->role,
            'password'          => Hash::make($request->password),
            'email_verified_at' => $request->boolean('verified') ? now() : null,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'     => ['required', 'in:admin,editor,viewer'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Prevent removing admin role from yourself
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $user->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => $request->role,
            'email_verified_at' => $request->boolean('verified')
                ? ($user->email_verified_at ?? now())
                : null,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}
