<?php

namespace App\Observers\Admin;

use App\Models\Package;
use App\Services\ActivityLogger;

class PackageObserver
{
    public function creating(Package $package)
    {
        if (app()->runningInConsole()) {
            return;
        }
        ActivityLogger::log("Package '{$package->name}' was created.");
    }

    public function updated(Package $package)
    {
        if (app()->runningInConsole()) {
            return;
        }
        ActivityLogger::log("Package '{$package->name}' was updated.");
    }

    public function deleted(Package $package)
    {
        if (app()->runningInConsole()) {
            return;
        }
        ActivityLogger::log("Package '{$package->name}' was deleted.");
    }
}
