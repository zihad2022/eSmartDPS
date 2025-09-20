<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\OtpCode;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ForgotPasswordPhoneController extends Controller
{
    /**
     * Inject SmsService to handle OTP sending.
     */
    public function __construct(private readonly SmsService $smsService) {}

    /**
     * Display the phone input form for forgotten password.
     */
    public function create(): View
    {
        return view('client.auth.forgot-password-phone');
    }

    /**
     * Handle the OTP sending process for a given phone number.
     */
    public function store(Request $request): RedirectResponse
    {
        // -----------------------------
        // 1. Validate incoming phone number
        // -----------------------------
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        // -----------------------------
        // 2. Find client by phone
        // -----------------------------
        $client = Client::where('phone', $request->phone)->first();
        if (!$client) {
            return back()->with('error', 'Client not found');
        }

        // -----------------------------
        // 3. Prevent multiple OTPs in short time
        // -----------------------------
        $existingOtp = OtpCode::where('userable_id', $client->id)
            ->where('userable_type', Client::class)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($existingOtp) {
            // Redirect to OTP verification if already sent
            return redirect()->route('client.otp.verify', ['phone' => $client->phone])
                ->with('error', 'OTP already sent. Please check your phone.');
        }

        // -----------------------------
        // 4. Generate a new OTP (4 digits)
        // -----------------------------
        $otp = rand(1000, 9999);

        // -----------------------------
        // 5. Store OTP in the database with 5-minute expiry
        // -----------------------------
        OtpCode::create([
            'userable_id' => $client->id,
            'userable_type' => Client::class,
            'phone' => $client->phone,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // -----------------------------
        // 6. Send OTP via SMS service
        // -----------------------------
        $this->smsService->sendOtp($otp, $client->phone, $client->first_name);

        // -----------------------------
        // 7. Redirect to OTP verification page with success message
        // -----------------------------
        return redirect()->route('client.otp.verify', ['phone' => $client->phone])
            ->with('success', 'OTP sent successfully. It will expire in 5 minutes.');
    }
}
