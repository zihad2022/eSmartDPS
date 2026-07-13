<?php

namespace App\Actions\Admin\Roles;

use App\Models\Admin;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GetRoleManagementDataAction
{
    public function execute(Admin $actor, Role|int|null $editRole = null, int $perPage = 10): array
    {
        if (is_int($editRole)) {
            $editRole = Role::query()
                ->where('guard_name', 'admin')
                ->with('permissions:id')
                ->findOrFail($editRole);
        }

        if ($editRole && $editRole->guard_name !== 'admin') {
            abort(404);
        }

        $actorPermissionIds = $actor->getAllPermissions()->pluck('id');
        $isSuperAdmin = $actor->hasRole('super-admin');

        if ($editRole && ! $this->canManage($editRole, $actorPermissionIds, $isSuperAdmin)) {
            abort(403, 'You cannot manage a role that grants permissions beyond your own account.');
        }

        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->when(! $isSuperAdmin, fn ($query) => $query->whereIn('id', $actorPermissionIds))
            ->orderBy('name')
            ->get();

        $roles = Role::query()
            ->with('permissions')
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $manageableRoleIds = $roles->getCollection()
            ->filter(fn (Role $role): bool => $this->canManage($role, $actorPermissionIds, $isSuperAdmin))
            ->pluck('id')
            ->all();

        return [
            'roles' => $roles,
            'groupedPermissions' => $this->groupPermissions($permissions),
            'editRole' => $editRole?->loadMissing('permissions'),
            'manageableRoleIds' => $manageableRoleIds,
        ];
    }

    private function canManage(Role $role, Collection $actorPermissionIds, bool $isSuperAdmin): bool
    {
        if ($role->name === 'super-admin') {
            return false;
        }

        return $isSuperAdmin
            || $role->permissions->pluck('id')->diff($actorPermissionIds)->isEmpty();
    }

    private function groupPermissions($permissions)
    {
        $categories = [
            'Dashboard' => ['dashboard'],
            'Clients' => ['clients'],
            'Packages' => ['packages'],
            'Invoices' => ['invoices'],
            'Tickets' => ['tickets', 'ticket chats', 'ticket messages'],
            'Users' => ['users', 'user activities'],
            'Roles' => ['roles'],
            'Settings' => ['settings'],
            'Data Exports' => ['export'],
            'Profile' => ['profile'],
        ];

        $grouped = collect(array_fill_keys(array_keys($categories), collect()));
        $grouped->put('Others', collect());

        foreach ($permissions as $permission) {
            $name = strtolower($permission->name);
            $category = collect($categories)
                ->first(fn (array $keywords) => collect($keywords)->contains(
                    fn (string $keyword): bool => str_contains($name, $keyword)
                ));

            $label = $category
                ? array_search($category, $categories, true)
                : 'Others';

            $grouped->get($label)->push($permission);
        }

        return $grouped->filter(fn ($items) => $items->isNotEmpty());
    }
}
