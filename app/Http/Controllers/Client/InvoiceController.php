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
        // 5. Calculate statistics (for this client only)
        // -----------------------------
        $totalInvoices = $client->invoices()->count();
        $totalPaidInvoices = $client->invoices()->where('status', InvoiceStatus::PAID)->count();
        $totalUnpaidInvoices = $client->invoices()->where('status', InvoiceStatus::UNPAID)->count();
        $totalRefundedInvoices = $client->invoices()->where('status', InvoiceStatus::REFUNDED)->count();
        $totalCancelledInvoices = $client->invoices()->where('status', InvoiceStatus::CANCELLED)->count();
        $totalRefundRequestedInvoices = $client->invoices()->where('status', InvoiceStatus::REFUND_REQUESTED)->count();

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
        // Ensure client can only see their own invoice
        if ($invoice->client_id !== owner_client_id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        return view('client.invoice.show', compact('invoice'));
    }
}
