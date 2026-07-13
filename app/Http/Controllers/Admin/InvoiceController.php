<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Invoices\CreateInvoiceAction;
use App\Actions\Admin\Invoices\DeleteInvoiceAction;
use App\Actions\Admin\Invoices\GetInvoiceDetailsAction;
use App\Actions\Admin\Invoices\GetInvoiceFormDataAction;
use App\Actions\Admin\Invoices\GetInvoicesAction;
use App\Actions\Admin\Invoices\UpdateInvoiceAction;
use App\Domain\Invoices\Models\Invoice;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvoiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request, GetInvoicesAction $action): View
    {
        return view('admin.invoice.index', $action->execute(
            search: $request->string('search')->trim()->toString() ?: null,
            status: $request->string('status')->toString() ?: null,
        ));
    }

    public function create(GetInvoiceFormDataAction $action): View
    {
        return view('admin.invoice.form', $action->execute());
    }

    public function store(InvoiceRequest $request, CreateInvoiceAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice, GetInvoiceDetailsAction $action): View
    {
        return view('admin.invoice.show', $action->execute($invoice));
    }

    public function edit(Invoice $invoice, GetInvoiceFormDataAction $action): View
    {
        return view('admin.invoice.form', $action->execute($invoice));
    }

    public function update(
        InvoiceRequest $request,
        Invoice $invoice,
        UpdateInvoiceAction $action
    ): RedirectResponse {
        $action->execute($invoice, $request->validated());

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice, DeleteInvoiceAction $action): RedirectResponse
    {
        try {
            $action->execute($invoice);
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
