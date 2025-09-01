<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Member;
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
        // 3. Get client settings
        // -----------------------------
        $settings = Auth::guard('client')->user()->settings;

        // -----------------------------
        // 4. Return dashboard view
        // -----------------------------
        return view('client.dashboard', compact('totalMembers', 'totalBalance', 'settings'));
    }
}
