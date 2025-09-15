<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\Client\PaymentExport;
use App\Services\ActivityLogger;
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
