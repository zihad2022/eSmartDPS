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
        $activities = Activity::query()
            ->with('causer')
            ->forClientAccount(owner_client_id())
            ->latest('id')
            ->paginate(10);
        return view('client.user.activities', compact('activities'));
    }
}
