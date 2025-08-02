<?php

namespace App\Observers;

use App\Models\Pakage;
use App\Services\ActivityLogger;

class PakageObserver
{
    public function creating(Pakage $pakage)
    {
        if (app()->runningInConsole()) {
            return;
        }
        ActivityLogger::log("Pakage '{$pakage->name}' was created.");
    }

    public function updated(Pakage $pakage)
    {
        if (app()->runningInConsole()) {
            return;
        }
        ActivityLogger::log("Pakage '{$pakage->name}' was updated.");
    }

    public function deleted(Pakage $pakage)
    {
        if (app()->runningInConsole()) {
            return;
        }
        ActivityLogger::log("Pakage '{$pakage->name}' was deleted.");
    }
}
