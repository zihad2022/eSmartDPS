<?php

namespace App\Domain\Invoices\Models;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'client_id',
        'package_id',
        'package_name',
        'package_description',
        'billing_start',
        'billing_end',
        'due_date',
        'invoice_number',
        'invoice_amount',
        'status',
        'paid_at',
        'payment_reference',
        'payment_id',
        'trx_id',
        'payment_method',
        'wallet_address',
    ];

    protected function casts(): array
    {
        return [
            'invoice_amount' => 'integer',
            'status' => InvoiceStatus::class,
            'payment_method' => PaymentMethod::class,
            'billing_start' => 'date',
            'billing_end' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::UNPAID);
    }

    public function scopeRefunded(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::REFUNDED);
    }

    public function scopeRefundRequested(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::REFUND_REQUESTED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::CANCELLED);
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID;
    }
}
