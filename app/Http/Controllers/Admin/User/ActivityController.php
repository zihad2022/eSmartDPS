<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $activities = Activity::with('causer')->where('causer_type', 'App\Models\Admin')->orderBy('id', 'desc')->paginate(10);

        return view('admin.user.activities', compact('activities'));
    }
}
