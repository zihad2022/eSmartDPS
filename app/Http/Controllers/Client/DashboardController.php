<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $totalMembers = Member::where('client_id', owner_client_id())->count();

        $totalBalance = Member::where('client_id', owner_client_id())->sum('total_balance');

        return view('client.dashboard', compact('totalMembers', 'totalBalance'));
    }
}
