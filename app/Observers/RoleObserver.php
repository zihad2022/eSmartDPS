<?php

namespace App\Observers;

use App\Services\ActivityLogger;
use Spatie\Permission\Models\Role;

class RoleObserver
{
    // Check here if role guard is admin than only create activity
    public function created(Role $role)
    {
        if (auth('admin')->check()) {
            ActivityLogger::log("Role '{$role->name}' was created.");
        }
    }

    public function deleted(Role $role)
    {
        if (auth('admin')->check()) {
            ActivityLogger::log("Role '{$role->name}' was deleted.");
        }
    }
}
