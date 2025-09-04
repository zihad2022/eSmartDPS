<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Client\ProjectExport;

class ProjectExportController extends Controller
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
            ? "Projects Exported with status: {$status}" 
            : "Projects Exported";
    
        // Log the activity
        ActivityLogger::log($logMessage);
    
        // Return the Excel download with the given status filter
        return Excel::download(new ProjectExport($status), 'projects.xlsx');
    }
}
