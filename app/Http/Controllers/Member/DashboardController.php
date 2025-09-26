<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ClientSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $member = Auth::guard('member')->user();
    
        $totalShares = 32;
        $monthlySavings = $totalShares * 100;
        $totalBalance = $member->payments()->sum('amount');
    
        // Get parent client via relationship
        $client = $member->client;
        $clientId = $client->id;
    
        $settings = ClientSetting::where('client_id', $clientId)->first();
    
        return view('member.dashboard', compact('totalShares', 'monthlySavings', 'totalBalance', 'settings'));
    }
    
}
