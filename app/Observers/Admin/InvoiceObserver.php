<?php

namespace App\Observers\Admin;

use App\Domain\Invoices\Models\Invoice;
use App\Services\ActivityLogger;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        ActivityLogger::log("Invoice '{$invoice->invoice_number}' was created.");
    }

    public function updated(Invoice $invoice): void
    {
        ActivityLogger::log("Invoice '{$invoice->invoice_number}' was updated.");
    }

    public function deleted(Invoice $invoice): void
    {
        ActivityLogger::log("Invoice '{$invoice->invoice_number}' was deleted.");
    }
}
