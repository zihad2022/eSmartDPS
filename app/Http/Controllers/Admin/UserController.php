<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a paginated list of admins with optional status filter.
     */
    public function index(Request $request): View
    {
        // -----------------------------
        // 1. Capture search and status filters
        // -----------------------------
        $search = $request->get('search');
        $status = $request->query('status');
    
        // -----------------------------
        // 2. Build admin query
        // -----------------------------
        $users = Admin::query()
            // Search filter
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('username', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            // Status filter
            ->when($status === 'active', fn ($q) => $q->where('status', true))
            ->when($status === 'inactive', fn ($q) => $q->where('status', false))
            // Order by latest
            ->latest('id')
            // Pagination
            ->paginate(10)
            ->appends($request->query());
    
        // -----------------------------
        // 3. Calculate statistics
        // -----------------------------
        $totalUsers = Admin::count();
        $activeUsers = Admin::where('status', true)->count();
        $inactiveUsers = Admin::where('status', false)->count();
    
        // -----------------------------
        // 4. Return view
        // -----------------------------
        return view('admin.user.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'search' // optional: to keep search input populated
        ));
    }
    

    /**
     * Show the form to create a new admin.
     */
    public function create(): View
    {
        // -----------------------------
        // 1. Fetch roles for admin guard
        // -----------------------------
        $roles = Role::where('guard_name', 'admin')
            // ->whereNotIn('name', ['super-admin'])
            ->get();

        // -----------------------------
        // 2. Return view
        // -----------------------------
        return view('admin.user.form', compact('roles'));
    }

    /**
     * Store a new admin in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        // -----------------------------
        // 1. Validate request data
        // -----------------------------
        $validated = $this->validateUser($request);

        // -----------------------------
        // 2. Create admin
        // -----------------------------
        $admin = Admin::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'profile_photo' => $validated['profile_photo'] ?? null,
            'status' => $validated['status'],
        ]);

        // -----------------------------
        // 3. Assign role
        // -----------------------------
        $admin->assignRole($validated['role']);

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin has been added successfully.');
    }

    /**
     * Show the form to edit an existing admin.
     */
    public function edit(Admin $user): View
    {
        // -----------------------------
        // 1. Fetch roles for admin guard
        // -----------------------------
        $roles = Role::where('guard_name', 'admin')
            // ->whereNotIn('name', ['super-admin'])
            ->get();

        // -----------------------------
        // 2. Return view
        // -----------------------------
        return view('admin.user.form', compact('user', 'roles'));
    }

    /**
     * Update an existing admin in the database.
     */
    public function update(Request $request, Admin $user): RedirectResponse
    {
        // -----------------------------
        // 1. Validate request data
        // -----------------------------
        $validated = $this->validateUser($request, $user->id);

        // -----------------------------
        // 2. Update admin
        // -----------------------------
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

        // -----------------------------
        // 3. Sync role
        // -----------------------------
        $user->syncRoles([$validated['role']]);

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin has been updated successfully.');
    }

    /**
     * Delete an admin.
     */
    public function destroy(Admin $user): RedirectResponse
    {
        // -----------------------------
        // 1. Prevent deleting super-admin
        // -----------------------------
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Super Admin cannot be deleted.');
        }

        // -----------------------------
        // 2. Delete admin
        // -----------------------------
        $user->delete();

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()->route('admin.users.index')
            ->with('success', 'Admin has been deleted successfully.');
    }

    /**
     * Helper: Validate admin data for store/update.
     */
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
