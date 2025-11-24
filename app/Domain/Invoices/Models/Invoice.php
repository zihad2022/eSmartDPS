<?php

namespace App\Domain\Invoices\Models;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
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
    protected $fillable = [
        'client_id',
        'package_id',

        // Package snapshot
        'package_name',
        'package_description',

        // Billing dates
        'billing_start',
        'billing_end',

        // Invoice
        'invoice_number',
        'invoice_amount',
        'status',
        'paid_at',

        // payment info
        'payment_reference',
        'payment_id',
        'trx_id',
        'payment_method',
        'wallet_address',
    ];

    /*--------------------------------
    | CASTS
    --------------------------------*/
    // Cast attributes to enums or other types
    protected $casts = [
        'status' => InvoiceStatus::class,         
        'payment_method' => PaymentMethod::class,
        'billing_start' => 'datetime',
        'billing_end' => 'datetime',
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

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
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
