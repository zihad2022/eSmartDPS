<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\OtpCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpVerifyController extends Controller
{
    /**
     * Show the OTP verification form.
     */
    public function create(): View
    {
        return view('client.auth.otp-verify');
    }

    /**
     * Handle OTP verification.
     */
    public function verify(Request $request): RedirectResponse
    {
        // -----------------------------
        // 1. Validate user input
        // -----------------------------
        $request->validate([
            'phone' => 'required|numeric',
            'otp' => 'required|numeric',
        ]);

        // -----------------------------
        // 2. Retrieve client by phone number
        // -----------------------------
        $client = Client::where('phone', $request->phone)->first();
        if (! $client) {
            return back()->with('error', 'Client not found.');
        }

        // -----------------------------
        // 3. Retrieve the latest unused OTP for this client
        // -----------------------------
        $otpCode = OtpCode::where('userable_id', $client->id)
            ->where('userable_type', Client::class)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->latest('expires_at') // ensure we pick the latest OTP if multiple exist
            ->first();

        if (! $otpCode) {
            return back()->with('error', 'Invalid OTP.');
        }

        // -----------------------------
        // 4. Check if OTP has expired
        // -----------------------------
        if ($otpCode->isExpired()) {
            return back()->with('error', 'OTP has expired.');
        }

        // -----------------------------
        // 5. Mark OTP as used and remove it from database
        // -----------------------------
        $otpCode->update(['is_used' => true]);
        $otpCode->delete();

        // -----------------------------
        // 6. Redirect to password reset page with phone number
        // -----------------------------
        return redirect()->route('client.password.reset', ['phone' => $client->phone])
            ->with('success', 'OTP verified. You can now reset your password.');
    }
}
