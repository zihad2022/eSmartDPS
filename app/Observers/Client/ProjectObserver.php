<?php

namespace App\Observers\Client;

use App\Models\Project;
use App\Services\ActivityLogger;

class ProjectObserver
{
    public function created(Project $project)
    {
        ActivityLogger::log("Project '{$project->name}' was created.");
    }

    public function updated(Project $project)
    {
        ActivityLogger::log("Project '{$project->name}' was updated.");
    }

    public function deleted(Project $project)
    {
        ActivityLogger::log("Project '{$project->name}' was deleted.");
    }
}
