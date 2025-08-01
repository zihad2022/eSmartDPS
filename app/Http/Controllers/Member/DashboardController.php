<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
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

        $memberShares = $member->memberShares()->with('share')->get();

        $totalShares = $memberShares->sum('shares_count');

        $monthlySavings = $memberShares->sum(function ($ms) {
            return $ms->share->price * $ms->shares_count;
        });

        $totalBalance = $member->payments()->sum('amount');

        return view('member.dashboard', compact('totalShares', 'monthlySavings', 'totalBalance'));
    }
}
