<?php

namespace App\Observers;

use App\Models\Admin;
use App\Services\ActivityLogger;

class AdminObserver
{
    public function created(Admin $admin)
    {
        ActivityLogger::log("User '{$admin->name}' was created.");
    }

    public function updated(Admin $admin)
    {
        ActivityLogger::log("User '{$admin->name}' was updated.");
    }

    public function deleted(Admin $admin)
    {
        ActivityLogger::log("User '{$admin->name}' was deleted.");
    }
}
