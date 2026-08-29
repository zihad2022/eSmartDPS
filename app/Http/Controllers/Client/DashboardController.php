<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Client;
use App\Models\Member;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the client dashboard display.
     */
    public function __invoke(Request $request): View
    {
        $clientId = owner_client_id();

        // Basic stats
        $totalMembers = Member::where('client_id', $clientId)->count();
        $totalBalance = (int) Member::where('client_id', $clientId)->sum('total_balance');
        $totalUsers = Client::where('parent_id', $clientId)->count() + 1;
        $totalProjects = Project::where('client_id', $clientId)->count();

        // Client settings
        $settings = Client::with('settings')->find($clientId)?->settings;

        // Recent activities (last 7 days)
        $recentActivities = Activity::with('causer')
            ->forClientAccount($clientId)
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(3)
            ->get();

        return view('client.dashboard', compact(
            'totalMembers',
            'totalBalance',
            'totalUsers',
            'totalProjects',
            'settings',
            'recentActivities'
        ));
    }
}
