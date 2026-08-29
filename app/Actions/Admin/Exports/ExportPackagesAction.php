<?php

namespace App\Actions\Admin\Exports;

use App\Exports\PackageExport;
use App\Services\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportPackagesAction
{
    public function execute(?string $status): BinaryFileResponse
    {
        ActivityLogger::log('Packages exported'.($status ? " with status: {$status}" : '.'));

        return Excel::download(new PackageExport($status), 'packages.xlsx');
    }
}
