<?php

namespace App\Actions\Admin\Invoices;

use App\Domain\Invoices\Models\Invoice;
use App\Enums\InvoiceStatus;
use Illuminate\Validation\ValidationException;

class DeleteInvoiceAction
{
    public function execute(Invoice $invoice): void
    {
        if (in_array($invoice->status, [InvoiceStatus::PAID, InvoiceStatus::REFUNDED], true)) {
            throw ValidationException::withMessages([
                'invoice' => ['Paid or refunded invoices are financial records and cannot be deleted. Cancel an unpaid invoice instead.'],
            ]);
        }

        $invoice->delete();
    }
}
