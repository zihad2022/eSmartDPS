<?php

namespace App\Http\Controllers\Member;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\MemberPaymentRequest;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function create(): View|RedirectResponse
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();

        $settings = Cache::remember("client_settings:{$member->client_id}", now()->addMinutes(5), function () use ($member) {
            return ClientSetting::where('client_id', $member->client_id)->first();
        });

        if (! $settings || empty($settings->payment_methods)) {
            return redirect()->back()->withErrors(['general' => 'Payment settings are not configured for your account.']);
        }

        $methods = collect($settings->payment_methods)
            ->map(fn ($value) => PaymentMethod::from($value));

        return view('member.payment', compact('methods'));
    }

    public function store(MemberPaymentRequest $request): RedirectResponse
    {
        /** @var Member $member */
        $member = Auth::guard('member')->user();
        $validated = $request->validated();

        return DB::transaction(function () use ($validated, $member, $request): RedirectResponse {
            $path = $request->file('receipt_file')->store('receipts', 'public');
            $amount = (int) round((float) $validated['payment_amount']);

            Payment::create([
                'payment_id' => generate_payment_id(),
                'client_id' => $member->client_id,
                'member_id' => $member->id,
                'amount' => $amount,
                'payment_method' => (int) $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'status' => PaymentStatus::PENDING->value,
                'paid_at' => $validated['payment_date'],
                'due_date' => null,
                'meta' => [
                    'notes' => $validated['payment_notes'] ?? null,
                    'receipt_path' => $path,
                    'submitted_ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);

            return redirect()->back()->with('success', 'Payment submitted successfully!');
        });
    }
}
