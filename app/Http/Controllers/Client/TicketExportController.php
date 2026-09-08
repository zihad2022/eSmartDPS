<?php

namespace App\Http\Controllers\Client;

use App\Exports\Client\TicketExport;
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
        $status = $request->query('status');

        ActivityLogger::log($status ? "Tickets Exported with status: {$status}" : 'Tickets Exported');

        return Excel::download(new TicketExport($status), 'tickets.xlsx');
    }
}
