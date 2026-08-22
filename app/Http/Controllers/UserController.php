<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GugusMutu;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'gugusMutu'])->latest()->paginate(15);
        return Inertia::render('Users/Index', [
            'users' => $users,
            'gugusMutus' => GugusMutu::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::all(),
            'gugusMutus' => GugusMutu::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name',
            'gugus_mutu_id' => 'nullable|exists:gugus_mutus,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gugus_mutu_id' => $request->gugus_mutu_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => Role::all(),
            'gugusMutus' => GugusMutu::all()
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|exists:roles,name',
            'gugus_mutu_id' => 'nullable|exists:gugus_mutus,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'gugus_mutu_id' => $request->gugus_mutu_id,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id !== auth()->id()) {
            $user->delete();
        }
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
