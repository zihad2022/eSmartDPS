<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'client_id',
        'invoice_number',
        'invoice_amount',
        'status',
        'payment_id',
        'trx_id',
        'payment_method',
        'wallet_address',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
    ];

    public function getInvoiceAmountAttribute($value)
    {
        return number_format($value);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', InvoiceStatus::UNPAID);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', InvoiceStatus::REFUNDED);
    }

    public function scopeRefundRequested($query)
    {
        return $query->where('status', InvoiceStatus::REFUND_REQUESTED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', InvoiceStatus::CANCELLED);
    }
}
