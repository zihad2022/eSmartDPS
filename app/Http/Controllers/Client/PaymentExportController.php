<?php

namespace App\Http\Controllers\Client;

use App\Exports\Client\PaymentExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $status = $request->input('status');
        ActivityLogger::log("Payments Exported with status: {$status}");

        return Excel::download(new PaymentExport($status), 'payments.xlsx');
    }
}
