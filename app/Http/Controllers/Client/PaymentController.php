<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $client = owner_client_id();

        $paymentsQuery = Payment::with('member')
            ->whereHas('member', fn ($q) => $q->where('client_id', $client));

        if ($request->has('status')) {
            $paymentsQuery->where('status', $request->status);
        }

        $payments = $paymentsQuery->latest()->paginate(10);

        // Summary Stats (cached if needed)
        $summary = Payment::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->whereHas('member', fn ($q) => $q->where('client_id', $client))
            ->groupBy('status')
            ->pluck('count', 'status');

        $totals = Payment::whereHas('member', fn ($q) => $q->where('client_id', $client))
            ->sum('amount');

        return view('client.payment.index', [
            'payments' => $payments,
            'totalPayments' => $summary->sum(),
            'pendingCount' => $summary[PaymentStatus::PENDING->value] ?? 0,
            'dueCount' => $summary[PaymentStatus::DUE->value] ?? 0,
            'paidCount' => $summary[PaymentStatus::PAID->value] ?? 0,
            'cancelledCount' => $summary[PaymentStatus::CANCELLED->value] ?? 0,
            'totalAmount' => $totals,
        ]);
    }

    public function edit($id)
    {
        $clientId = owner_client_id();

        $payment = Payment::with('member')
            ->where('id', $id)
            ->whereHas('member', fn ($q) => $q->where('client_id', $clientId))
            ->firstOrFail();

        $members = Member::where('client_id', $clientId)->select('id', 'name', 'member_id')->get();

        return view('client.payment.form', compact('payment', 'members'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'integer', 'in:'.implode(',', array_column(PaymentStatus::cases(), 'value'))],
        ]);

        $clientId = owner_client_id();

        $payment = Payment::where('id', $id)
            ->whereHas('member', fn ($q) => $q->where('client_id', $clientId))
            ->firstOrFail();

        $payment->update(['status' => $request->status]);

        return redirect()->route('client.payments.index')
            ->with('success', 'Payment status updated successfully.');
    }
}
