<?php

namespace App\Actions\Admin\Roles;

use App\Models\Admin;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    public function execute(Admin $actor, Role $role): void
    {
        abort_unless($role->guard_name === 'admin', 404);

        if ($role->name === 'super-admin') {
            throw ValidationException::withMessages([
                'role' => ['The Super Admin role cannot be deleted.'],
            ]);
        }

        $role->loadMissing('permissions:id');
        if (! $actor->hasRole('super-admin') && $role->permissions->pluck('id')->diff($actor->getAllPermissions()->pluck('id'))->isNotEmpty()) {
            throw ValidationException::withMessages([
                'role' => ['You cannot delete a role that grants permissions beyond your own account.'],
            ]);
        }

        if ($role->users()->exists()) {
            throw ValidationException::withMessages([
                'role' => ['This role is assigned to users. Reassign them before deleting the role.'],
            ]);
        }

        $role->delete();
    }
}
