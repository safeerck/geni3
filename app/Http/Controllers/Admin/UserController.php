<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
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

        $users    = $query->latest()->paginate(10)->withQueryString();
        $total    = User::count();
        $verified = User::whereNotNull('email_verified_at')->count();
        $byRole   = User::selectRaw('role, count(*) as count')->groupBy('role')->pluck('count', 'role');

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

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => $request->role,
            'password'          => Hash::make($request->password),
            'email_verified_at' => $request->boolean('verified') ? now() : null,
        ]);

        ActivityLogger::log(
            action:      'user.created',
            description: auth()->user()->name . " created user {$user->name} with role {$user->role}",
            targetType:  'user',
            targetId:    $user->id,
            targetName:  $user->name,
            properties:  ['role' => $user->role, 'email' => $user->email],
        );

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

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $oldRole = $user->role;
        $changes = [];

        if ($user->name !== $request->name)  $changes['name']  = ['from' => $user->name,  'to' => $request->name];
        if ($user->email !== $request->email) $changes['email'] = ['from' => $user->email, 'to' => $request->email];
        if ($user->role !== $request->role)   $changes['role']  = ['from' => $oldRole,     'to' => $request->role];

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
            $changes['password'] = ['from' => '(hidden)', 'to' => '(changed)'];
        }

        // Role change gets its own action type
        $action = isset($changes['role']) ? 'user.role_changed' : 'user.updated';

        $desc = isset($changes['role'])
            ? auth()->user()->name . " changed {$user->name}'s role from {$oldRole} to {$request->role}"
            : auth()->user()->name . " updated {$user->name}";

        ActivityLogger::log(
            action:      $action,
            description: $desc,
            targetType:  'user',
            targetId:    $user->id,
            targetName:  $user->name,
            properties:  $changes ?: null,
        );

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log(
            action:      'user.deleted',
            description: auth()->user()->name . " deleted user {$name}",
            targetType:  'user',
            targetId:    null,
            targetName:  $name,
        );

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}
