<?php

namespace App\Actions\Admin\Roles;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UpdateRoleAction
{
    public function execute(Admin $actor, Role $role, array $data): Role
    {
        $this->guard($actor, $role);

        return DB::transaction(function () use ($actor, $role, $data): Role {
            $permissions = $this->allowedPermissions($actor, $data['permissions'] ?? []);

            $role->update(['name' => $data['name']]);
            $role->syncPermissions($permissions);

            return $role->refresh()->load('permissions');
        });
    }

    private function guard(Admin $actor, Role $role): void
    {
        abort_unless($role->guard_name === 'admin', 404);

        if ($role->name === 'super-admin') {
            throw ValidationException::withMessages([
                'role' => ['The Super Admin role cannot be changed.'],
            ]);
        }

        $role->loadMissing('permissions:id');
        if (! $actor->hasRole('super-admin') && $role->permissions->pluck('id')->diff($actor->getAllPermissions()->pluck('id'))->isNotEmpty()) {
            throw ValidationException::withMessages([
                'role' => ['You cannot modify a role that grants permissions beyond your own account.'],
            ]);
        }
    }

    private function allowedPermissions(Admin $actor, array $permissionIds)
    {
        $permissions = Permission::query()
            ->where('guard_name', 'admin')
            ->whereIn('id', $permissionIds)
            ->get();

        if ($actor->hasRole('super-admin')) {
            return $permissions;
        }

        $actorPermissionIds = $actor->getAllPermissions()->pluck('id');
        if ($permissions->pluck('id')->diff($actorPermissionIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'permissions' => ['You can only grant permissions already assigned to your own account.'],
            ]);
        }

        return $permissions;
    }
}
