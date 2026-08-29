<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Exports\ExportTicketsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketExportController extends Controller
{
    public function __invoke(Request $request, ExportTicketsAction $action): BinaryFileResponse
    {
        return $action->execute($request->string('status')->toString() ?: null);
    }
}
