<?php

namespace App\Http\Controllers\Member;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\ClientSetting;
use App\Models\Payment;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function create()
    {
        $member = Auth::guard('member')->user();
        if (! $member) {
            return redirect()->back()->withErrors(['general' => 'You must be logged in as a member to submit payments.']);
        }

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

    public function store(Request $request)
    {
        $member = Auth::guard('member')->user();
        if (! $member) {
            return redirect()->back()->withErrors(['general' => 'You must be logged in as a member to submit payments.']);
        }

        $methodValues = array_map(fn ($m) => $m->value, PaymentMethod::cases());

        $validated = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required','date'],
            'payment_method' => ['required', Rule::in($methodValues)],
            'reference_number' => ['nullable','string','max:64'],
            'payment_notes' => ['nullable','string','max:1000'],
            'receipt_file' => ['required','file','mimes:jpg,jpeg,png,pdf','max:5120'],
        ]);

        // Persist proof and record atomically
        return DB::transaction(function () use ($validated, $member, $request) {
            // Save receipt file
            $path = $request->file('receipt_file')->store('receipts', 'public');

            // Monetary values are stored as whole currency units across the schema.
            $amount = (int) round((float) $validated['payment_amount']);

            Payment::create([
                'payment_id'      => generate_payment_id(),
                'client_id'       => $member->client_id,
                'member_id'       => $member->id,
                'amount' => $amount,
                'payment_method'  => (int) $validated['payment_method'],
                'reference_number'=> $validated['reference_number'] ?? null,
                'status'          => PaymentStatus::PENDING->value,
                'paid_at'         => $validated['payment_date'],
                'due_date'        => null,
                'meta'            => [
                    'notes' => $validated['payment_notes'] ?? null,
                    'receipt_path' => $path,
                    'submitted_ip' => $request->ip(),
                    'user_agent'   => $request->userAgent(),
                ],
            ]);

            return redirect()->back()->with('success', 'Payment submitted successfully!');
        });
    }
}
