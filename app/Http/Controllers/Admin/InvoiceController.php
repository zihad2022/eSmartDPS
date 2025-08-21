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
        // Start query with eager loading to avoid N+1 problem when accessing client
        $query = Invoice::with('client')->latest('id');

        // Map status string to Enum for cleaner code
        $statusMap = [
            'unpaid' => InvoiceStatus::UNPAID,
            'paid' => InvoiceStatus::PAID,
            'refunded' => InvoiceStatus::REFUNDED,
            'cancelled' => InvoiceStatus::CANCELLED,
            'refund-request' => InvoiceStatus::REFUND_REQUESTED,
        ];

        // If request has a valid status, filter by it
        if ($status = $request->query('status')) {
            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
        }

        // Paginate results (10 per page) and preserve filters in pagination links
        $invoices = $query->paginate(10)->withQueryString();

        return view('admin.invoice.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $clients = Client::parents()->select('id', 'first_name', 'last_name')->get(); // lighter query
        $invoice_number = generate_invoice_number();

        return view('admin.invoice.form', compact('clients', 'invoice_number'));
    }

    /**
     * Store a newly created invoice in database.
     */
    public function store(Request $request)
    {
        // Validate request inputs
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'invoice_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:1,2,3,4,5'], // enum values
            'payment_id' => ['nullable', 'string', 'max:255'],
            'trx_id' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'wallet_address' => ['nullable', 'string', 'max:255'],
        ]);

        // Always auto-generate invoice number instead of relying on user input
        $validated['invoice_number'] = generate_invoice_number();

        // Mass assignment (ensure model has $fillable defined properly)
        Invoice::create($validated);

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display a specific invoice by ID.
     */
    public function show(string $id)
    {
        // Load invoice with its client
        $invoice = Invoice::with('client')->findOrFail($id);

        return view('admin.invoice.show', compact('invoice'));
    }

    /**
     * Show the form for editing an existing invoice.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::with('client')->findOrFail($id);
        $clients = Client::parents()->select('id', 'first_name', 'last_name')->get();
        $invoice_number = $invoice->invoice_number; // keep existing number

        return view('admin.invoice.form', compact('invoice', 'clients', 'invoice_number'));
    }

    /**
     * Update an existing invoice.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Validate inputs (same as store)
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'invoice_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:1,2,3,4,5'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'trx_id' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'wallet_address' => ['nullable', 'string', 'max:255'],
        ]);

        $invoice->update($validated);

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Delete an invoice from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()
            ->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
