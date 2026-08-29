<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Exports\ExportClientsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClientExportController extends Controller
{
    public function __invoke(Request $request, ExportClientsAction $action): BinaryFileResponse
    {
        return $action->execute($request->string('status')->toString() ?: null);
    }
}
