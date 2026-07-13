<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Invoices\SendInvoiceAction;
use App\Domain\Invoices\Models\Invoice;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class InvoiceSendToClientController extends Controller
{
    public function __invoke(Invoice $invoice, SendInvoiceAction $action): RedirectResponse
    {
        try {
            $action->execute($invoice);
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return back()->with('success', 'Invoice has been sent to the client successfully.');
    }
}
