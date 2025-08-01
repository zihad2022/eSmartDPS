<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = Admin::latest()->get();

        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        return view('admin.user.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);

        Admin::create(array_merge($validated, [
            'password' => Hash::make($validated['password']),
        ]));

        return redirect()->route('admin.users.index')->with('success', 'Admin has been added successfully.');
    }

    public function edit(Admin $user)
    {
        return view('admin.user.form', compact('user'));
    }

    public function update(Request $request, Admin $user)
    {
        $validated = $this->validateUser($request, $user->id);

        $user->update(array_merge($validated, [
            'password' => filled($validated['password'])
                ? Hash::make($validated['password'])
                : $user->password,
        ]));

        return redirect()->route('admin.users.index')->with('success', 'Admin has been updated successfully.');
    }

    public function destroy(Admin $user)
    {
        if ($user->id === 1) {
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
            'profile_photo_path' => ['nullable', 'string'],
            'role' => ['required', Rule::in(['admin', 'manager', 'editor'])],
            'status' => ['required', 'boolean'],
        ]);
    }
}
