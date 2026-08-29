<?php

namespace App\Http\Controllers\Client;

use App\Exports\Client\MemberExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MemberExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Get the status query parameter from the request
        $status = $request->query('status');

        // Prepare a log message based on the status
        $logMessage = $status
            ? "Members Exported with status: {$status}"
            : 'Members Exported';

        // Log the activity
        ActivityLogger::log($logMessage);

        // Return the Excel download with the given status filter
        return Excel::download(new MemberExport($status), 'members.xlsx');
    }
}
