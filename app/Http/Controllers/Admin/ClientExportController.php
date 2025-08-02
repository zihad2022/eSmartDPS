<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientsExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClientExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $status = $request->query('status');

        ActivityLogger::log("Clients Exported with status: {$status}");

        return Excel::download(new ClientsExport($status), 'clients.xlsx');
    }
}
