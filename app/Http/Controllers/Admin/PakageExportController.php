<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PakageExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PakageExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $status = $request->query('status');

        ActivityLogger::log("Pakage Exported with status: {$status}");

        return Excel::download(new PakageExport($status), 'pakages.xlsx');
    }
}
