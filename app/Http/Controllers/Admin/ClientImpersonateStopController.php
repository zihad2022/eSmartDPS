<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ClientImpersonateStopController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        if (!Session::has('impersonate_admin_id')) {
            return redirect()->route('admin.dashboard');
        }

        $adminId = Session::pull('impersonate_admin_id');

        // Logout from client guard
        Auth::guard('client')->logout();

        // Login back as admin
        Auth::guard('admin')->loginUsingId($adminId);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Impersonation stopped. Back to admin dashboard.');
    }
}
