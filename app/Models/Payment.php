<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /*--------------------------------
    | MASS ASSIGNABLE
    --------------------------------*/
    protected $fillable = [
        'payment_id',      // Unique payment code
        'client_id',       // Linked client
        'member_id',       // Linked member/customer
        'amount',          // Payment amount
        'payment_method',  // e.g., cash, card, bank
        'transaction_id',  // Gateway transaction ID
        'reference',       // Invoice/order reference
        'status',          // Payment status
        'paid_at',         // When paid
        'due_date',        // When due
        'meta',            // Extra data/notes
    ];

    /*--------------------------------
    | CASTS
    --------------------------------*/
    protected $casts = [
        'amount' => 'integer',
        'payment_method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
        'meta' => 'array', // JSON data
    ];

    /*--------------------------------
    | RELATIONSHIPS
    --------------------------------*/
    
    // Payment belongs to a client
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Payment belongs to a member/customer
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /*--------------------------------
    | SCOPES
    --------------------------------*/

    // Only pending payments
    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    // Only due payments
    public function scopeDue($query)
    {
        return $query->where('status', PaymentStatus::DUE);
    }

    // Only paid payments
    public function scopePaid($query)
    {
        return $query->where('status', PaymentStatus::PAID);
    }

        public function scopeCancelled($query)
    {
        return $query->where('status', PaymentStatus::CANCELLED);
    }

    /*--------------------------------
    | HELPERS
    --------------------------------*/

    // Check if payment is paid
    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    // Check if payment is due
    public function isDue(): bool
    {
        return $this->status === PaymentStatus::DUE;
    }
}
