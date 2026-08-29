<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ClientSetting;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function __invoke(Request $request): View
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        // Fetch client settings once using relationship or client_id
        $settings = ClientSetting::where('client_id', $member->client_id)->first();

        if (! $settings) {
            abort(404, 'Client settings not found.');
        }

        // Basic member financial summary
        $totalShares = $member->share_quantity;
        $monthlySavings = $totalShares * $settings->share_price;
        $totalBalance = $member->total_balance;

        return view('member.dashboard', compact(
            'totalShares',
            'monthlySavings',
            'totalBalance',
            'settings'
        ));
    }
}
