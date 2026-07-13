<?php

namespace App\Actions\Admin\Clients;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StopClientImpersonationAction
{
    public function execute(): bool
    {
        $adminId = Session::pull('impersonate_admin_id');

        if (! $adminId) {
            return false;
        }

        $admin = Admin::query()->active()->find($adminId);
        Auth::guard('client')->logout();

        if (! $admin) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return false;
        }

        Auth::guard('admin')->login($admin);
        request()->session()->regenerate();

        return true;
    }
}
