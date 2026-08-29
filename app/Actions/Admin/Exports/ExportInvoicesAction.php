<?php

namespace App\Actions\Admin\Exports;

use App\Exports\InvoiceExport;
use App\Services\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportInvoicesAction
{
    public function execute(?string $status): BinaryFileResponse
    {
        ActivityLogger::log('Invoices exported'.($status ? " with status: {$status}" : '.'));

        return Excel::download(new InvoiceExport($status), 'invoices.xlsx');
    }
}
