<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;


class RolePermissionController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get(); // Load users with their roles
        $roles = Role::with('permissions')->get(); // Load roles with their permissions
        $permissions = Permission::all(); // Get all permissions

        return view('roles.index', compact('users', 'roles', 'permissions'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Update user info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        // Assign role
        $user->syncRoles([$request->role]);

        // If Admin, assign ALL permissions
        if ($request->role === 'Admin') {
            $allPermissions = Permission::pluck('name')->toArray();
            $user->syncPermissions($allPermissions);
        } else {
            // Assign selected permissions
            $user->syncPermissions($request->permissions ?? []);
        }

        return redirect()->route('roles.index')->with('success', 'User updated successfully!');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = \Spatie\Permission\Models\Role::all();
        $permissions = \Spatie\Permission\Models\Permission::all();

        return view('roles.edit', compact('user', 'roles', 'permissions'));
    }

    public function show($id)
    {
        $user = \App\Models\User::with('roles', 'permissions')->findOrFail($id);
        return view('roles.show', compact('user'));
    }


}
