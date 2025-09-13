<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     * Supports filtering by status via query string (?status=paid, unpaid, refunded, etc.)
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1. Handle search query
        // -----------------------------
        // Search invoices by multiple fields including client name, invoice number, trx_id, package name, etc.
        $search = $request->get('search');
    
        // -----------------------------
        // 2. Initialize invoice query
        // -----------------------------
        // Start query with eager loading of client to avoid N+1 problem
        $query = Invoice::with('client')->latest('id');
    
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
                  ->orWhere('wallet_address', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q) use ($search) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]) // Full name
                      ->orWhere('first_name', 'like', "%{$search}%")                           
                      ->orWhere('last_name', 'like', "%{$search}%")                             
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }
    
        // -----------------------------
        // 4. Filter by status (optional)
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
        // 5. Calculate invoice statistics
        // -----------------------------
        $totalInvoices = Invoice::count();
        $totalPaidInvoices = Invoice::where('status', InvoiceStatus::PAID)->count();
        $totalUnpaidInvoices = Invoice::where('status', InvoiceStatus::UNPAID)->count();
        $totalRefundedInvoices = Invoice::where('status', InvoiceStatus::REFUNDED)->count();
        $totalCancelledInvoices = Invoice::where('status', InvoiceStatus::CANCELLED)->count();
        $totalRefundRequestedInvoices = Invoice::where('status', InvoiceStatus::REFUND_REQUESTED)->count();
    
        // -----------------------------
        // 6. Paginate results
        // -----------------------------
        $invoices = $query->paginate(10)->withQueryString();
    
        // -----------------------------
        // 7. Return view with data
        // -----------------------------
        return view('admin.invoice.index', compact(
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
    

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        // -----------------------------
        // 1. Fetch clients
        // -----------------------------
        // Only fetch parent clients for dropdown selection (lighter query)
        $clients = Client::parents()->select('id', 'first_name', 'last_name')->get();

        // -----------------------------
        // 2. Generate unique invoice number
        // -----------------------------
        $invoice_number = generate_invoice_number();

        // -----------------------------
        // 3. Return form view
        // -----------------------------
        return view('admin.invoice.form', compact('clients', 'invoice_number'));
    }

    /**
     * Store a newly created invoice in the database.
     */
    public function store(Request $request)
    {
        // -----------------------------
        // 1. Validate input
        // -----------------------------
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'invoice_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:1,2,3,4,5'], // Enum values
            'payment_id' => ['nullable', 'string', 'max:255'],
            'trx_id' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'wallet_address' => ['nullable', 'string', 'max:255'],
        ]);

        // Always generate invoice number server-side
        $validated['invoice_number'] = generate_invoice_number();

        // -----------------------------
        // 2. Create invoice
        // -----------------------------
        Invoice::create($validated);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display a specific invoice by ID.
     */
    public function show(string $id)
    {
        // -----------------------------
        // 1. Fetch invoice with client
        // -----------------------------
        $invoice = Invoice::with('client')->findOrFail($id);

        // -----------------------------
        // 2. Return view
        // -----------------------------
        return view('admin.invoice.show', compact('invoice'));
    }

    /**
     * Show the form for editing an existing invoice.
     */
    public function edit(string $id)
    {
        // -----------------------------
        // 1. Fetch invoice and clients
        // -----------------------------
        $invoice = Invoice::with('client')->findOrFail($id);
        $clients = Client::parents()->select('id', 'first_name', 'last_name')->get();

        // -----------------------------
        // 2. Keep current invoice number
        // -----------------------------
        $invoice_number = $invoice->invoice_number;

        // -----------------------------
        // 3. Return form view
        // -----------------------------
        return view('admin.invoice.form', compact('invoice', 'clients', 'invoice_number'));
    }

    /**
     * Update an existing invoice.
     */
    public function update(Request $request, string $id)
    {
        // -----------------------------
        // 1. Fetch invoice
        // -----------------------------
        $invoice = Invoice::findOrFail($id);

        // -----------------------------
        // 2. Validate input
        // -----------------------------
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'invoice_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:1,2,3,4,5'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'trx_id' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'wallet_address' => ['nullable', 'string', 'max:255'],
        ]);

        // -----------------------------
        // 3. Update invoice
        // -----------------------------
        $invoice->update($validated);

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Delete an invoice from storage.
     */
    public function destroy(string $id)
    {
        // -----------------------------
        // 1. Fetch invoice
        // -----------------------------
        $invoice = Invoice::findOrFail($id);

        // -----------------------------
        // 2. Delete invoice
        // -----------------------------
        $invoice->delete();

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
