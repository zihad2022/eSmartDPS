<?php

namespace App\Observers\Admin;

use App\Services\ActivityLogger;
use Spatie\Permission\Models\Role;

class RoleObserver
{
    public function created(Role $role): void
    {
        if ($role->guard_name === 'admin') {
            ActivityLogger::log("Role '{$role->name}' was created.");
        }
    }

    public function updated(Role $role): void
    {
        if ($role->guard_name === 'admin') {
            ActivityLogger::log("Role '{$role->name}' was updated.");
        }
    }

    public function deleted(Role $role): void
    {
        if ($role->guard_name === 'admin') {
            ActivityLogger::log("Role '{$role->name}' was deleted.");
        }
    }
}
