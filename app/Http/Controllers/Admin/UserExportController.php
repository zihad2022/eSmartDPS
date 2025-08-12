<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\UserExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $status = $request->query('status');

        ActivityLogger::log("Users Exported with status: {$status}");

        return Excel::download(new UserExport($status), 'users.xlsx');
    }
}
