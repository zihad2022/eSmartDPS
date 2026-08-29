<?php

namespace App\Observers\Admin;

use App\Models\Package;
use App\Services\ActivityLogger;

class PackageObserver
{
    public function created(Package $package): void
    {
        ActivityLogger::log("Package '{$package->name}' was created.");
    }

    public function updated(Package $package): void
    {
        ActivityLogger::log("Package '{$package->name}' was updated.");
    }

    public function deleted(Package $package): void
    {
        ActivityLogger::log("Package '{$package->name}' was deleted.");
    }
}
