<?php

namespace App\Actions\Admin\Roles;

use App\Models\Admin;
use Spatie\Permission\Models\Role;

class GetRoleDetailsAction
{
    public function execute(Admin $actor, Role $role): Role
    {
        abort_unless($role->guard_name === 'admin', 404);

        $role->load(['permissions', 'users']);
        $canManage = $role->name !== 'super-admin'
            && ($actor->hasRole('super-admin')
                || $role->permissions->pluck('id')->diff($actor->getAllPermissions()->pluck('id'))->isEmpty());

        return $role->setAttribute('can_be_managed_by_current_admin', $canManage);
    }
}
