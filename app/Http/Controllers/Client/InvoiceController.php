<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $clientId = owner_client_id(); // helper for logged-in client id
        $client = Client::findOrFail($clientId);

        // -----------------------------
        // 1. Handle search query
        // -----------------------------
        $search = $request->get('search');

        // -----------------------------
        // 2. Initialize invoice query (only for this client)
        // -----------------------------
        $query = $client->invoices()->latest('id');

        // -----------------------------
        // 3. Apply search filter (if search exists)
        // -----------------------------
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('package_name', 'like', "%{$search}%")
                    ->orWhere('package_description', 'like', "%{$search}%")
                    ->orWhere('trx_id', 'like', "%{$search}%")
                    ->orWhere('payment_id', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhere('wallet_address', 'like', "%{$search}%");
            });
        }

        // -----------------------------
        // 4. Optional: Filter by status
        // -----------------------------
        $statusMap = [
            'unpaid' => InvoiceStatus::UNPAID,
            'paid' => InvoiceStatus::PAID,
            'refunded' => InvoiceStatus::REFUNDED,
            'cancelled' => InvoiceStatus::CANCELLED,
            'refund-request' => InvoiceStatus::REFUND_REQUESTED,
        ];

        if ($status = $request->query('status')) {
            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
        }

        // -----------------------------
        // 5. Calculate statistics in a single aggregated query
        // -----------------------------
        $stats = Invoice::query()
            ->where('client_id', $clientId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as paid', [InvoiceStatus::PAID->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as unpaid', [InvoiceStatus::UNPAID->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as refunded', [InvoiceStatus::REFUNDED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled', [InvoiceStatus::CANCELLED->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as refund_requested', [InvoiceStatus::REFUND_REQUESTED->value])
            ->first();

        $totalInvoices = (int) ($stats->total ?? 0);
        $totalPaidInvoices = (int) ($stats->paid ?? 0);
        $totalUnpaidInvoices = (int) ($stats->unpaid ?? 0);
        $totalRefundedInvoices = (int) ($stats->refunded ?? 0);
        $totalCancelledInvoices = (int) ($stats->cancelled ?? 0);
        $totalRefundRequestedInvoices = (int) ($stats->refund_requested ?? 0);

        // -----------------------------
        // 6. Paginate results
        // -----------------------------
        $invoices = $query->paginate(10)->withQueryString();

        // -----------------------------
        // 7. Return view
        // -----------------------------
        return view('client.invoice.index', compact(
            'invoices',
            'totalInvoices',
            'totalPaidInvoices',
            'totalUnpaidInvoices',
            'totalRefundedInvoices',
            'totalCancelledInvoices',
            'totalRefundRequestedInvoices',
            'search'
        ));
    }

    public function show(Invoice $invoice)
    {
        authorize_owner($invoice);

        return view('client.invoice.show', compact('invoice'));
    }
}
