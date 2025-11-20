<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validate the form data
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to find the admin
        $admin = Admin::where('email', $request->email)->first();

        // Check the password
        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // check is status is false
        if ($admin->status == false) {
            throw ValidationException::withMessages([
                'email' => ['Your account is disabled. Please contact the administrator.'],
            ]);
        }

        // Log the admin in
        Auth::guard('admin')->login($admin);

        // update last_login 
        $admin->last_login = now();
        $admin->save();
        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');
    }
}
