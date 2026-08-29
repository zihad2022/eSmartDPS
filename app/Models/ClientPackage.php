<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPackage extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'client_id',
        'package_id',
        'starts_at',
        'ends_at',
        'is_trial',
        'is_active',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_trial' => 'boolean',
            'is_active' => 'boolean',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('status', self::STATUS_ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('ends_at', '<=', now());
    }

    public function scopeTrial(Builder $query): Builder
    {
        return $query->where('is_trial', true);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('is_trial', false);
    }

    public function isActive(): bool
    {
        return $this->is_active
            && $this->status === self::STATUS_ACTIVE
            && ! $this->isExpired()
            && ! $this->starts_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->ends_at->isPast();
    }
}
