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
        'date' => 'date',
        'amount' => 'decimal:2',
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
