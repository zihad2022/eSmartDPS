<?php

namespace App\Actions\Admin\Users;

use App\Models\Admin;
use Spatie\Permission\Models\Role;

class GetAdminFormDataAction
{
    public function __construct(
        private readonly GuardAdminAccountManagementAction $guardManagement,
    ) {}

    public function execute(Admin $actor, ?Admin $admin = null): array
    {
        if ($admin) {
            $this->guardManagement->execute($actor, $admin);
        }

        $permissionIds = $actor->getAllPermissions()->pluck('id');

        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->when(! $actor->hasRole('super-admin'), function ($query) use ($permissionIds): void {
                $query->where('name', '!=', 'super-admin')
                    ->whereDoesntHave('permissions', function ($permissions) use ($permissionIds): void {
                        if ($permissionIds->isEmpty()) {
                            return;
                        }

                        $permissions->whereNotIn('permissions.id', $permissionIds);
                    });
            })
            ->orderBy('name')
            ->get();

        return [
            'user' => $admin?->load('roles'),
            'roles' => $roles,
        ];
    }
}
