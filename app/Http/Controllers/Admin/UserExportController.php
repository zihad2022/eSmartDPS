<?php
namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Exports\ExportUsersAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserExportController extends Controller
{
    public function __invoke(Request $request, ExportUsersAction $action): BinaryFileResponse
    {
        return $action->execute($request->string('status')->toString() ?: null);
    }
}
