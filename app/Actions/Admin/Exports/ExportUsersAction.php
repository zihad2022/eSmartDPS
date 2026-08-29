<?php

namespace App\Actions\Admin\Exports;

use App\Exports\Admin\UserExport;
use App\Services\ActivityLogger;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportUsersAction
{
    public function execute(?string $status): BinaryFileResponse
    {
        ActivityLogger::log('Users exported'.($status ? " with status: {$status}" : '.'));

        return Excel::download(new UserExport($status), 'users.xlsx');
    }
}
