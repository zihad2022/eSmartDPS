<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'payment_id',
        'member_id',
        'amount',
        'date',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
        'amount' => 'integer',
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function scopeDue($query)
    {
        return $query->where('status', PaymentStatus::DUE);
    }

    public function scopePaid($query)
    {
        return $query->where('status', PaymentStatus::PAID);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', PaymentStatus::CANCELLED);
    }
}
