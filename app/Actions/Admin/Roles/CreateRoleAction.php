<?php

namespace App\Actions\Admin\Roles;

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    public function execute(Admin $actor, array $data): Role
    {
        return DB::transaction(function () use ($actor, $data): Role {
            $permissions = $this->allowedPermissions($actor, $data['permissions'] ?? []);

            $role = Role::query()->create([
                'name' => $data['name'],
                'guard_name' => 'admin',
            ]);

            $role->syncPermissions($permissions);

            return $role->load('permissions');
        });
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
