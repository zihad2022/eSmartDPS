<?php

namespace App\Exports;

use App\Domain\Invoices\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Class InvoiceExport
 *
 * This class is responsible for exporting invoices to an Excel file.
 * It uses Maatwebsite\Excel to generate an export with custom headings and row mappings.
 */
class InvoiceExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Serial counter for exported rows.
     *
     * @var int
     */
    private int $sl = 1;

    /**
     * Invoice status filter (paid, unpaid, refunded, etc.).
     *
     * @var string|null
     */
    private $status;

    /**
     * Create a new export instance with a given status filter.
     *
     * @param string|null $status
     */
    public function __construct($status = null)
    {
        $this->status = $status;
    }

    /**
     * Get the collection of invoices based on the provided status.
     * This will be the data source for the export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Invoice::orderBy('id', 'desc');

        // Apply status filter if provided
        if ($this->status === 'paid') {
            return $query->paid()->get();
        }

        if ($this->status === 'unpaid') {
            return $query->unpaid()->get();
        }

        if ($this->status === 'refunded') {
            return $query->refunded()->get();
        }

        if ($this->status === 'refund-requested') {
            return $query->refundRequested()->get();
        }

        if ($this->status === 'cancelled') {
            return $query->cancelled()->get();
        }

        // If no status filter, return all invoices
        return $query->get();
    }

    /**
     * Define the column headings for the Excel export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'SL',                // Serial Number
            'Invoice Number',    // Unique invoice number
            'Client Name',       // Full name of the client
            'Invoice Amount',    // Total invoice amount
            'Status',            // Invoice status
            'Payment ID',        // Payment reference ID
            'Trx ID',            // Transaction ID
            'Payment Method',    // Payment method (Bank, PayPal, etc.)
            'Wallet Address',    // Wallet address if crypto or digital wallet
            'Created At',        // Invoice creation date
            'Updated At',        // Last update date
        ];
    }

    /**
     * Map each invoice model into an array of values for export.
     *
     * @param Invoice $invoice
     * @return array
     */
    public function map($invoice): array
    {
        // Eager load client relation to avoid multiple queries
        $invoice->load('client');

        return [
            $this->sl++, // Increment serial number
            $invoice->invoice_number,
            optional($invoice->client)->first_name . ' ' . optional($invoice->client)->last_name,
            $invoice->invoice_amount,
            $invoice->status?->label() ?? '-', // Convert enum or status object to label
            $invoice->payment_id,
            $invoice->trx_id,
            $invoice->payment_method?->label() ?? '-', // Convert payment method enum to label
            $invoice->wallet_address,
            $invoice->created_at,
            $invoice->updated_at,
        ];
    }
}
