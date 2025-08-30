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
        $status = $request->query('status');

        ActivityLogger::log("Members Exported with status: {$status}");

        return Excel::download(new MemberExport($status), 'members.xlsx');
    }
}
