<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\TicketExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TicketExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $status = $request->input('status');
        ActivityLogger::log("Tickets Exported with status: {$status}");

        return Excel::download(new TicketExport($status), 'tickets.xlsx');
    }
}
