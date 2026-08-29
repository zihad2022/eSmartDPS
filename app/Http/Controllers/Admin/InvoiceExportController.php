<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Exports\ExportInvoicesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceExportController extends Controller
{
    public function __invoke(Request $request, ExportInvoicesAction $action): BinaryFileResponse
    {
        return $action->execute($request->string('status')->toString() ?: null);
    }
}
