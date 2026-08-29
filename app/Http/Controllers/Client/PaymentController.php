<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Display a paginated list of payments with optional status filter.
     */
    public function index(Request $request)
    {
        $clientId = owner_client_id();
        $search = $request->get('search'); // Capture search query

        // -----------------------------
        // 1. Build base payments query
        // -----------------------------
        $paymentsQuery = Payment::with('member')
            ->where('client_id', $clientId)
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('payment_id', 'like', "%{$search}%")
                        ->orWhere('transaction_id', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($mq) => $mq->where('name', 'like', "%{$search}%")
                            ->orWhere('member_id', 'like', "%{$search}%"));
                });
            });

        // -----------------------------
        // 2. Apply status filter if provided
        // -----------------------------
        if ($request->has('status')) {
            $paymentsQuery->where('status', $request->status);
        }

        // -----------------------------
        // 3. Paginate results
        // -----------------------------
        $payments = $paymentsQuery->latest()->paginate(10)->appends($request->query());

        // -----------------------------
        // 4. Prepare summary stats
        // -----------------------------
        $summary = Payment::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->where('client_id', $clientId)
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalAmount = Payment::where('client_id', $clientId)
            ->sum('amount');

        // -----------------------------
        // 5. Fetch client settings
        // -----------------------------
        $settings = ClientSetting::where('client_id', $clientId)->first();

        // -----------------------------
        // 6. Return view with data
        // -----------------------------
        return view('client.payment.index', [
            'payments' => $payments,
            'totalPayments' => $summary->sum(),
            'pendingCount' => $summary[PaymentStatus::PENDING->value] ?? 0,
            'dueCount' => $summary[PaymentStatus::DUE->value] ?? 0,
            'paidCount' => $summary[PaymentStatus::PAID->value] ?? 0,
            'cancelledCount' => $summary[PaymentStatus::CANCELLED->value] ?? 0,
            'totalAmount' => $totalAmount,
            'settings' => $settings,
            'search' => $search, // pass search to view
        ]);
    }

    /**
     * Show a specific payment details.
     */
    public function show($id)
    {
        $clientId = owner_client_id();

        // -----------------------------
        // 1. Fetch payment ensuring ownership
        // -----------------------------
        $payment = Payment::with('member')
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        // -----------------------------
        // 2. Fetch client settings
        // -----------------------------
        $settings = ClientSetting::where('client_id', $clientId)->first();

        // -----------------------------
        // 3. Return view
        // -----------------------------
        return view('client.payment.show', compact('payment', 'settings'));
    }

    /**
     * Show form to edit a payment.
     */
    public function edit($id)
    {
        $clientId = owner_client_id();

        // -----------------------------
        // 1. Fetch payment ensuring ownership
        // -----------------------------
        $payment = Payment::with('member')
            ->where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        // -----------------------------
        // 2. Fetch all members for this client
        // -----------------------------
        $members = Member::where('client_id', $clientId)
            ->select('id', 'name', 'member_id')
            ->get();

        // -----------------------------
        // 3. Return view
        // -----------------------------
        return view('client.payment.form', compact('payment', 'members'));
    }

    /**
     * Update payment status.
     */
    public function update(Request $request, $id)
    {
        $clientId = owner_client_id();

        // -----------------------------
        // 1. Validate input
        // -----------------------------
        $validated = $request->validate([
            'status' => ['required', Rule::enum(PaymentStatus::class)],
        ]);

        // -----------------------------
        // 2. Fetch payment ensuring ownership
        // -----------------------------
        DB::transaction(function () use ($id, $clientId, $validated): void {
            $payment = Payment::query()
                ->where('id', $id)
                ->where('client_id', $clientId)
                ->lockForUpdate()
                ->firstOrFail();

            $member = $payment->member()->lockForUpdate()->firstOrFail();
            $previousStatus = $payment->status;
            $newStatus = PaymentStatus::from($validated['status']);

            if ($previousStatus !== PaymentStatus::PAID && $newStatus === PaymentStatus::PAID) {
                $member->increment('total_balance', $payment->amount);
            } elseif ($previousStatus === PaymentStatus::PAID && $newStatus !== PaymentStatus::PAID) {
                $member->update([
                    'total_balance' => max(0, $member->total_balance - $payment->amount),
                ]);
            }

            $payment->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === PaymentStatus::PAID
                    ? ($payment->paid_at ?? now())
                    : null,
            ]);
        });

        // -----------------------------
        // 5. Redirect with success
        // -----------------------------
        return redirect()->route('client.payments.index')
            ->with('success', 'Payment status updated successfully.');
    }

    /**
     * Delete a payment.
     */
    public function destroy($id)
    {
        $clientId = owner_client_id();

        // -----------------------------
        // 1. Fetch payment ensuring ownership
        // -----------------------------
        $payment = Payment::where('id', $id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        // -----------------------------
        // 2. Delete payment
        // -----------------------------
        $payment->delete();

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()->route('client.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
