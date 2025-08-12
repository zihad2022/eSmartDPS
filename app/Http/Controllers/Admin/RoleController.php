<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a paginated list of roles with permissions,
     * optionally loading a role for editing.
     */
    public function index(Request $request)
    {
        // Fetch roles with their permissions for the 'admin' guard
        $roles = Role::with('permissions')
            ->where('guard_name', 'admin')
            ->paginate(10);

        // Fetch all permissions for the 'admin' guard
        $permissions = Permission::where('guard_name', 'admin')->get();

        // Load role for editing if 'edit' query param exists
        $editRole = $request->has('edit')
            ? Role::with('permissions')
                ->where('guard_name', 'admin')
                ->findOrFail($request->query('edit'))
            : null;

        /**
         * Permission categories with associated keywords.
         * The order here determines display order in UI.
         */
        $categories = [
            'dashboard' => 'Dashboard',
            'clients' => 'Clients',
            'packages' => 'Packages',
            'invoices' => 'Invoices',
            'tickets' => 'Tickets', // Ticket Chats will be grouped here
            'users' => 'Users',     // User Activities will be grouped here
            'roles' => 'Roles',
            'settings' => 'Settings',
            'export' => 'Data Exports',
            'profile' => 'Profile',
        ];

        // Group permissions
        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            $foundCategory = null;

            foreach ($categories as $keyword => $label) {
                $permName = strtolower($permission->name);

                // Special handling so "ticket chats" and "ticket messages" are under Tickets
                if (str_contains($permName, 'ticket chats') || str_contains($permName, 'ticket messages')) {
                    $foundCategory = 'Tickets';
                    break;
                }

                // Special handling so "user activities" is under Users
                if (str_contains($permName, 'user activities')) {
                    $foundCategory = 'Users';
                    break;
                }

                // Default matching by keyword
                if (str_contains($permName, strtolower($keyword))) {
                    $foundCategory = $label;
                    break;
                }
            }

            // Assign to "Others" if no keyword match
            $foundCategory = $foundCategory ?: 'Others';

            // Add to grouped list
            $groupedPermissions[$foundCategory][] = $permission;
        }

        // Keep category order consistent
        $groupedPermissions = collect($groupedPermissions)
            ->sortBy(fn ($value, $key) => array_search($key, array_values($categories)) !== false
                    ? array_search($key, array_values($categories))
                    : 999
            );

        return view('admin.role.index', compact(
            'roles',
            'groupedPermissions',
            'editRole'
        ));
    }

    /**
     * Store a newly created role along with selected permissions.
     */
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Create new role for 'admin' guard
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'admin',
        ]);

        // Attach selected permissions (if any)
        $permissions = Permission::whereIn('id', $request->input('permissions', []))
            ->where('guard_name', 'admin')
            ->get();

        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully with selected permissions.');
    }

    /**
     * Update an existing role and sync its permissions.
     */
    public function update(Request $request, Role $role)
    {
        // Ensure this role belongs to 'admin' guard
        if ($role->guard_name !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Prevent modifications to the super-admin role
        if ($role->name === 'super-admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be updated.');
        }

        // Validate input data
        $request->validate([
            'name' => 'required|string|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Update role name
        $role->update([
            'name' => $request->name,
        ]);

        // Sync updated permissions
        $permissions = Permission::whereIn('id', $request->input('permissions', []))
            ->where('guard_name', 'admin')
            ->get();

        $role->syncPermissions($permissions);

        // Log activity
        ActivityLogger::log("Role '{$role->name}' was updated.");

        return back()->with('success', 'Role updated successfully with selected permissions.');
    }

    /**
     * Delete a role from the system.
     */
    public function destroy(Role $role)
    {
        // Ensure this role belongs to 'admin' guard
        if ($role->guard_name !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Prevent deleting the super-admin role
        if ($role->name === 'super-admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Super Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
