<?php
namespace App\Actions\Admin\Exports;

use App\Exports\Admin\TicketExport;
use App\Services\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportTicketsAction
{
    public function execute(?string $status): BinaryFileResponse
    {
        ActivityLogger::log('Tickets exported'.($status ? " with status: {$status}" : '.'));
        return Excel::download(new TicketExport($status), 'tickets.xlsx');
    }
}
