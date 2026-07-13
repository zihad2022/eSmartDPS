<?php

namespace App\Actions\Admin\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateAdminAction
{
    public function execute(Request $request, string $email, string $password, bool $remember = false): Admin
    {
        $admin = Admin::query()->where('email', $email)->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $admin->status) {
            throw ValidationException::withMessages([
                'email' => ['Your account is disabled. Please contact the administrator.'],
            ]);
        }

        Auth::guard('admin')->login($admin, $remember);
        $request->session()->regenerate();
        $request->session()->put('admin_last_activity', now()->timestamp);

        $admin->forceFill(['last_login' => now()])->saveQuietly();

        return $admin;
    }
}
