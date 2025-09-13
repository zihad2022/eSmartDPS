<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\Request;

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
            ->whereHas('member', fn($q) => $q->where('client_id', $clientId))
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('payment_id', 'like', "%{$search}%")
                        ->orWhere('transaction_id', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhereHas('member', fn($mq) => $mq->where('name', 'like', "%{$search}%")
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
            ->whereHas('member', fn($q) => $q->where('client_id', $clientId))
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalAmount = Payment::whereHas('member', fn($q) => $q->where('client_id', $clientId))
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
            ->whereHas('member', fn($q) => $q->where('client_id', $clientId))
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
            ->whereHas('member', fn($q) => $q->where('client_id', $clientId))
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
        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_column(PaymentStatus::cases(), 'value'))],
        ]);

        // -----------------------------
        // 2. Fetch payment ensuring ownership
        // -----------------------------
        $payment = Payment::where('id', $id)
            ->whereHas('member', fn($q) => $q->where('client_id', $clientId))
            ->firstOrFail();

        // -----------------------------
        // 3. Update status
        // -----------------------------
        $payment->update(['status' => $request->status]);

        // -----------------------------
        // 4. If paid, record timestamp & update member balance
        // -----------------------------
        if ($request->status === PaymentStatus::PAID->value) {
            $payment->paid_at = now();
            $payment->save();

            $payment->member->update([
                'total_balance' => $payment->member->total_balance + $payment->amount,
            ]);
        }

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
            ->whereHas('member', fn($q) => $q->where('client_id', $clientId))
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
