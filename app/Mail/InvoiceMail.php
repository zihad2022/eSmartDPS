<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public string $currency = 'BDT',
        public string $siteName = 'eSmartDPS',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Invoice #{$this->invoice->invoice_number}");
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.client.invoice',
            with: [
                'invoice' => $this->invoice,
                'currency' => $this->currency,
                'siteName' => $this->siteName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
