<?php

namespace App\Models;

use App\Domain\Clients\Models\Client;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'payment_id',
        'client_id',
        'member_id',
        'amount',
        'payment_method',
        'transaction_id',
        'reference_number',
        'reference', // backward-compatible alias for reference_number
        'status',
        'paid_at',
        'due_date',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'payment_method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'due_date' => 'date',
            'meta' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::DUE);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::PAID);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::CANCELLED);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function isDue(): bool
    {
        return $this->status === PaymentStatus::DUE;
    }

    protected function reference(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->reference_number,
            set: fn (?string $value): array => ['reference_number' => $value],
        );
    }
}
