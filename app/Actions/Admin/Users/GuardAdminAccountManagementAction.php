<?php

namespace App\Actions\Admin\Users;

use App\Models\Admin;
use Illuminate\Auth\Access\AuthorizationException;

class GuardAdminAccountManagementAction
{
    public function execute(Admin $actor, Admin $target): void
    {
        $actor->loadMissing('roles.permissions');
        $target->loadMissing('roles.permissions');

        if ($actor->hasRole('super-admin')) {
            return;
        }

        if ($target->hasRole('super-admin')) {
            throw new AuthorizationException('Only a Super Admin can manage a Super Admin account.');
        }

        $actorPermissionIds = $actor->getAllPermissions()->pluck('id');
        $targetPermissionIds = $target->getAllPermissions()->pluck('id');

        if ($targetPermissionIds->diff($actorPermissionIds)->isNotEmpty()) {
            throw new AuthorizationException('You cannot manage an administrator with permissions beyond your own account.');
        }
    }

    public function allows(Admin $actor, Admin $target): bool
    {
        try {
            $this->execute($actor, $target);

            return true;
        } catch (AuthorizationException) {
            return false;
        }
    }
}
