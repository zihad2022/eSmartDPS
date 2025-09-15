<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // -----------------------------
        // 1. Fetch activities
        // -----------------------------
        $activities = Activity::with('causer')->where('causer_type', 'App\Models\Client')->orderBy('id', 'desc')->paginate(10);

        // -----------------------------
        // 2. Return view
        // -----------------------------
        return view('client.user.activities', compact('activities'));
    }
}
