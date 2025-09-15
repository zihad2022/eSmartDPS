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
        // Fetch all members belonging to the authenticated client
        // and count them to display in the dashboard stats card.
        $totalMembers = Member::where('client_id', owner_client_id())->count();

        // -----------------------------
        // 2. Calculate total balance
        // -----------------------------
        // Sum up the 'total_balance' field of all members for this client.
        // This value represents the total accumulated balance across all members.
        $totalBalance = Member::where('client_id', owner_client_id())->sum('total_balance');

        // -----------------------------
        // 2. Calculate total users
        // -----------------------------
        // Sum up the 'total_balance' field of all members for this client.
        // This value represents the total accumulated balance across all members.
        $childrenUsers = Client::where('parent_id', owner_client_id())->count();
        $totalUsers = $childrenUsers + 1;

        // -----------------------------
        // 3. Calculate total projects
        // -----------------------------
        // Sum up the 'total_balance' field of all members for this client.
        // This value represents the total accumulated balance across all members.
        $totalProjects = Project::where('client_id', owner_client_id())->count();

        // -----------------------------
        // 4. Get client settings
        // -----------------------------
        // Retrieve the currently authenticated client and their associated
        // settings, which include currency, organization details, and more.
        $settings = Auth::guard('client')->user()->settings;

        // -----------------------------
        // 5. Return dashboard view
        // -----------------------------
        // Pass the collected data to the dashboard Blade view.
        // 'compact' is used to automatically create an array with variable names as keys.
        return view('client.dashboard', compact('totalMembers', 'totalBalance',  'totalUsers', 'totalProjects', 'settings'));
    }
}
