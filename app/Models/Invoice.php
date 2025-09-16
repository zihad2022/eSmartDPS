<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Invoice Model
 *
 * Represents an invoice in the system.
 * Each invoice belongs to a client and stores payment-related information.
 */
class Invoice extends Model
{
    /*--------------------------------
    | MASS ASSIGNABLE
    --------------------------------*/
    // Fields that can be mass-assigned (e.g., via create or update)
    protected $fillable = [
        'client_id',        // Linked client ID
        'invoice_number',   // Unique invoice number
        'invoice_amount',   // Total amount of invoice
        'status',           // Invoice status (enum: InvoiceStatus)
        'payment_id',       // Reference to payment record
        'trx_id',           // Transaction ID
        'payment_method',   // Payment method used (enum: PaymentMethod)
        'wallet_address',   // Wallet address (if crypto or wallet-based payment)
    ];

    /*--------------------------------
    | CASTS
    --------------------------------*/
    // Cast attributes to enums or other types
    protected $casts = [
        'status' => InvoiceStatus::class,           // Casts status to InvoiceStatus enum
        'payment_method' => PaymentMethod::class,   // Casts payment_method to PaymentMethod enum
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /*--------------------------------
    | RELATIONSHIPS
    --------------------------------*/

    // Each invoice belongs to one client
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /*--------------------------------
    | SCOPES (query helpers)
    --------------------------------*/

    // Only invoices that are Paid
    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    // Only invoices that are Unpaid
    public function scopeUnpaid($query)
    {
        return $query->where('status', InvoiceStatus::UNPAID);
    }

    // Only invoices that are Refunded
    public function scopeRefunded($query)
    {
        return $query->where('status', InvoiceStatus::REFUNDED);
    }

    // Only invoices with Refund Requested
    public function scopeRefundRequested($query)
    {
        return $query->where('status', InvoiceStatus::REFUND_REQUESTED);
    }

    // Only invoices that are Cancelled
    public function scopeCancelled($query)
    {
        return $query->where('status', InvoiceStatus::CANCELLED);
    }
}
