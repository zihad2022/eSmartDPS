<?php

namespace App\Http\Controllers\Client;

use App\Exports\Client\UserExport;
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
        $role = $request->query('role');
        $status = $request->query('status');

        // Determine log message based on filters
        // if ($role && $status) {
        //     $logMessage = "Users Exported with role: {$role} and status: {$status}";
        // } elseif ($role) {
        //     $logMessage = "Users Exported with role: {$role}";
        // } elseif (!is_null($status)) {
        //     $logMessage = "Users Exported with status: {$status}";
        // } else {
        //     $logMessage = "All Users Exported (no filters applied)";
        // }

        ActivityLogger::log('Users Exported');

        return Excel::download(
            new UserExport($role, $status),
            'users.xlsx'
        );
    }
}
