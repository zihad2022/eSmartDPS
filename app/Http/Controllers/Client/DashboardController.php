<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Member;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the client dashboard.
     */
    public function __invoke(Request $request)
    {
        // -----------------------------
        // 1. Count total members
        // -----------------------------
        $totalMembers = Member::where('client_id', owner_client_id())->count();

        // -----------------------------
        // 2. Calculate total balance
        // -----------------------------
        $totalBalance = Member::where('client_id', owner_client_id())->sum('total_balance');

        // -----------------------------
        // 2. Calculate total users
        // -----------------------------
        $childrenUsers = Client::where('parent_id', owner_client_id())->count();
        $totalUsers = $childrenUsers + 1;

        // -----------------------------
        // 3. Calculate total projects
        // -----------------------------
        $totalProjects = Project::where('client_id', owner_client_id())->count();

        // -----------------------------
        // 4. Get the main client settings
        // -----------------------------
        $settings = Client::find(owner_client_id())->settings;

        // -----------------------------
        // 5. Return dashboard view
        // -----------------------------
        return view('client.dashboard', compact('totalMembers', 'totalBalance',  'totalUsers', 'totalProjects', 'settings'));
    }
}
