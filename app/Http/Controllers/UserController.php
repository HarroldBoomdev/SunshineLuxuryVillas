<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{

    public function dashboard() {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    }

    public function store(Request $request)
    {
        \Log::info('Store method called');
        \Log::info('Request data:', $request->all());

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/[a-z]/',    // at least one lowercase letter
                    'regex:/[A-Z]/',    // at least one uppercase letter
                    'regex:/[0-9]/',    // at least one number
                    'regex:/[\W]/',     // at least one special character
                    'confirmed',
                ],
                'role' => 'required|exists:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            \Log::info('Validation passed');

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            \Log::info('User created', ['user' => $user]);

            // Assign role
            $user->assignRole($request->role);

            // Assign permissions if provided
            if (!empty($request->permissions)) {
                $user->syncPermissions($request->permissions);
            }

            \Log::info('User role and permissions assigned', ['user' => $user]);

            // Redirect to roles index route
            return redirect()->route('admin.roles')->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            \Log::error('User creation failed', ['error' => $e->getMessage()]);
            return redirect()->route('admin.roles')->with('error', 'An error occurred during user creation.');
        }
    }


    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('roles.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        // dd($permissions);  // This will help debug if permissions are being fetched correctly.
        return view('roles.edit', compact('user', 'roles', 'permissions'));
    }



    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('roles.destroy')->with('success', 'User deleted successfully.');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    public function hasPermissionTo($permission)
    {
        return $this->permissions->contains('name', $permission);
    }


}
