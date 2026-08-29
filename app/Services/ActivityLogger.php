<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;

class ActivityLogger
{
    /**
     * Log a user activity into the "activities" table.
     *
     * @param  string  $activity  A description of the activity being logged
     */
    public static function log(string $activity): void
    {
        // Determine if an admin or client is logged in.
        // It first checks the admin guard, otherwise checks the client guard.
        $user = Auth::guard('admin')->check()
            ? Auth::guard('admin')->user()
            : (Auth::guard('client')->check() ? Auth::guard('client')->user() : null);

        // If no user is logged in, do not log anything.
        if (! $user) {
            return;
        }

        // Use Jenssegers Agent to detect browser, version, and OS platform.
        $agent = new Agent;

        // Create a new activity record with relevant details.
        Activity::create([
            'causer_id' => $user->id,               // ID of the logged-in user
            'causer_type' => get_class($user),       // User model type (Admin or Client)
            'activity' => $activity,              // Description of the activity
            'ip_address' => Request::ip(),          // IP address of the user
            'browser' => $agent->browser(),      // Browser name (e.g. Chrome)
            'version' => $agent->version($agent->browser()), // Browser version
            'system' => $agent->platform(),     // Operating system (e.g. Windows, Mac)
            'activity_date' => now(),                  // Timestamp of the activity
        ]);
    }
}
