<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InvoiceExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $status = $request->query('status');

        ActivityLogger::log("Invoice Exported with status: {$status}");

        return Excel::download(new InvoiceExport($status), 'invoices.xlsx');
    }
}
