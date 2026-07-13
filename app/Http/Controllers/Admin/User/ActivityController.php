<?php

namespace App\Http\Controllers\Admin\User;

use App\Actions\Admin\Activities\GetAdminActivitiesAction;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __invoke(GetAdminActivitiesAction $action): View
    {
        return view('admin.user.activities', [
            'activities' => $action->execute(),
        ]);
    }
}
