<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('client')->orderBy('id', 'desc');

        if ($status = $request->query('status')) {
            $statusMap = [
                'unpaid' => InvoiceStatus::UNPAID,
                'paid' => InvoiceStatus::PAID,
                'refunded' => InvoiceStatus::REFUNDED,
                'cancelled' => InvoiceStatus::CANCELLED,
                'refund-request' => InvoiceStatus::REFUND_REQUESTED,
            ];

            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
        }

        $invoices = $query->paginate(10)->appends($request->query());

        return view('admin.invoice.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::all();
        $invoice_number = generate_invoice_number();

        return view('admin.invoice.form', compact('clients', 'invoice_number'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'invoice_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:1,2,3,4,5'],
            'payment_id' => ['nullable', 'string', 'max:255'],
            'trx_id' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'wallet_address' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['invoice_number'] = generate_invoice_number();
        Invoice::create($validated);

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(string $id)
    {
        $invoice = Invoice::with('client')->findOrFail($id);

        return view('admin.invoice.show', compact('invoice'));
    }

    public function edit(string $id)
    {
        $invoice = Invoice::with('client')->findOrFail($id);
        $clients = Client::all();
        $invoice_number = $invoice->invoice_number;

        return view('admin.invoice.form', compact('invoice', 'clients', 'invoice_number'));
    }

    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

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

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
