<?php

namespace App\Actions\Admin\Invoices;

use App\Actions\Admin\Settings\ConfigureAdminMailAction;
use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Domain\Invoices\Models\Invoice;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class SendInvoiceAction
{
    public function __construct(
        private readonly ConfigureAdminMailAction $configureMail,
        private readonly GetAdminSettingsAction $getSettings,
    ) {
    }

    public function execute(Invoice $invoice): void
    {
        $invoice->loadMissing('client');

        if (! $invoice->client?->email) {
            throw ValidationException::withMessages([
                'invoice' => ['The client does not have an email address.'],
            ]);
        }

        try {
            $settings = $this->getSettings->execute();
            $configured = $this->configureMail->execute($settings);
            $mailer = $configured ? Mail::mailer('smtp') : Mail::mailer();

            $mailer->to($invoice->client->email)->send(new InvoiceMail(
                invoice: $invoice,
                currency: $settings->currency ?: 'BDT',
                siteName: $settings->site_name ?: config('app.name'),
            ));
        } catch (Throwable $exception) {
            Log::error('Invoice email delivery failed.', [
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'invoice' => ['The invoice could not be sent. Verify the SMTP settings and try again.'],
            ]);
        }
    }
}
