<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClientsExportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }
}
