<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Exports\ExportPackagesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PackageExportController extends Controller
{
    public function __invoke(Request $request, ExportPackagesAction $action): BinaryFileResponse
    {
        return $action->execute($request->string('status')->toString() ?: null);
    }
}
