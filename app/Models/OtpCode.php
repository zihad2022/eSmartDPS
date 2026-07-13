<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'userable_id',
        'userable_type',
        'phone',
        'otp',
        'expires_at',
        'is_used',
    ];

    protected $hidden = [
        'otp',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUsable(Builder $query): Builder
    {
        return $query
            ->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    public function isExpired(): bool
    {
        return ! $this->expires_at || $this->expires_at->isPast();
    }
}
