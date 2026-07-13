<?php
namespace App\Actions\Admin\Exports;

use App\Exports\ClientsExport;
use App\Services\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportClientsAction
{
    public function execute(?string $status): BinaryFileResponse
    {
        ActivityLogger::log('Clients exported'.($status ? " with status: {$status}" : '.'));
        return Excel::download(new ClientsExport($status), 'clients.xlsx');
    }
}
