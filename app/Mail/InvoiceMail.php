<?php

namespace App\Mail;

use App\Models\AdminSetting;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $currency;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, ?string $currency = null)
    {
        $this->invoice = $invoice;

        // -----------------------------
        // 1. Set currency (fallback to admin setting if not provided)
        // -----------------------------
        $this->currency = $currency ?? AdminSetting::query()->value('currency') ?? '$';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice #{$this->invoice->invoice_number}"
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.client.invoice',
            with: [
                'invoice' => $this->invoice,
                'currency' => $this->currency,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
