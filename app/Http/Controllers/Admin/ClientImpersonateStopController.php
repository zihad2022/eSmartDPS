<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Clients\StopClientImpersonationAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class ClientImpersonateStopController extends Controller
{
    public function __invoke(StopClientImpersonationAction $action): RedirectResponse
    {
        if (! $action->execute()) {
            return redirect()->route('admin.login')
                ->with('error', 'The impersonation session is no longer valid.');
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Impersonation stopped. Back to admin dashboard.');
    }
}
