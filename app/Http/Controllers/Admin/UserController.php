<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $users = Admin::query()
            ->when($status === 'active', fn ($q) => $q->where('status', true))
            ->when($status === 'inactive', fn ($q) => $q->where('status', false))
            ->latest('id')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        // Only roles for 'admin' guard except 'super-admin'
        $roles = Role::where('guard_name', 'admin')
            // ->whereNotIn('name', ['super-admin'])
            ->get();

        return view('admin.user.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);

        $admin = Admin::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'profile_photo' => $validated['profile_photo'] ?? null,
            'status' => $validated['status'],
        ]);

        // Assign the selected role
        $admin->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'Admin has been added successfully.');
    }

    public function edit(Admin $user)
    {
        $roles = Role::where('guard_name', 'admin')
            // ->whereNotIn('name', ['super-admin'])
            ->get();

        return view('admin.user.form', compact('user', 'roles'));
    }

    public function update(Request $request, Admin $user)
    {
        $validated = $this->validateUser($request, $user->id);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? $user->username,
            'email' => $validated['email'] ?? $user->email,
            'phone' => $validated['phone'] ?? $user->phone,
            'password' => filled($validated['password'])
                ? Hash::make($validated['password'])
                : $user->password,
            'profile_photo' => $validated['profile_photo'] ?? $user->profile_photo,
            'status' => $validated['status'],
        ]);

        // Update role
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Admin has been updated successfully.');
    }

    public function destroy(Admin $user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.users.index')->with('error', 'Super Admin cannot be deleted.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Admin has been deleted successfully.');
    }

    protected function validateUser(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                $ignoreId ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('admins', 'username')->ignore($ignoreId),
            ],
            'email' => [
                $ignoreId ? 'sometimes' : 'required',
                'email',
                Rule::unique('admins', 'email')->ignore($ignoreId),
            ],
            'phone' => [
                $ignoreId ? 'sometimes' : 'required',
                'string',
                'max:20',
                Rule::unique('admins', 'phone')->ignore($ignoreId),
            ],
            'password' => [$ignoreId ? 'nullable' : 'required', 'string', 'min:6'],
            'profile_photo' => ['nullable', 'string'],
            'role' => [
                'required',
                Rule::in(Role::where('guard_name', 'admin')
                    // ->whereNotIn('name', ['super-admin'])
                    ->pluck('name')
                    ->toArray()
                ),
            ],
            'status' => ['required', 'boolean'],
        ]);
    }
}
