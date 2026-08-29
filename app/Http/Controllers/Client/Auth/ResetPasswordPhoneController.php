<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ResetPasswordPhoneController extends Controller
{
    /**
     * Show the reset password form.
     *
     * Expects a `phone` query parameter (passed after OTP verification).
     */
    public function create(Request $request): View
    {
        // -----------------------------
        // 1. Retrieve phone number from query string
        // -----------------------------
        $phone = $request->get('phone');

        // -----------------------------
        // 2. Render view with phone number pre-filled
        // -----------------------------
        return view('client.auth.reset-password-phone', compact('phone'));
    }

    /**
     * Handle the password reset request.
     */
    public function store(Request $request): RedirectResponse
    {
        // -----------------------------
        // 1. Validate input fields
        // -----------------------------
        $request->validate([
            'phone' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // -----------------------------
        // 2. Locate client by phone number
        // -----------------------------
        $client = Client::where('phone', $request->phone)->first();

        if (! $client) {
            return back()->with('error', 'Client not found');
        }

        // -----------------------------
        // 3. Update client password securely
        // -----------------------------
        $client->update([
            'password' => Hash::make($request->password), // Use Hash facade
        ]);

        // -----------------------------
        // 4. Redirect back to login with success message
        // -----------------------------
        return redirect()
            ->route('client.login')
            ->with('success', 'Password reset successfully. You can now login with your new password.');
    }
}
