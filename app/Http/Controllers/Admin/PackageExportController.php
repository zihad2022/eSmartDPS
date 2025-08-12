<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PackageExport;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PackageExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $status = $request->query('status');

        ActivityLogger::log("Package Exported with status: {$status}");

        return Excel::download(new PackageExport($status), 'packages.xlsx');
    }
}
