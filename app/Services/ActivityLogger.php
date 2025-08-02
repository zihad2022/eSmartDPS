<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;

class ActivityLogger
{
    public static function log($activity)
    {
        // check if admin or client is logged in
        $user = Auth::guard('admin')->check()
            ? Auth::guard('admin')->user()
            : (Auth::guard('client')->check() ? Auth::guard('client')->user() : null);

        // if no user is logged in, return
        if (! $user) {
            return;
        }

        $agent = new Agent();

        Activity::create([
            'causer_id' => $user->id,
            'causer_type' => get_class($user),
            'activity' => $activity,
            'ip_address' => Request::ip(),
            'browser' => $agent->browser(),
            'version' => $agent->version($agent->browser()),
            'system' => $agent->platform(),
            'activity_date' => now(),
        ]);
    }
}
