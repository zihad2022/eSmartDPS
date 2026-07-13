<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Dashboard\GetDashboardDataAction;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(GetDashboardDataAction $action): View
    {
        return view('admin.dashboard', $action->execute(auth('admin')->user()));
    }
}
