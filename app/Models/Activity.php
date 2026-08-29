<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    protected $fillable = [
        'causer_id',
        'causer_type',
        'activity',
        'ip_address',
        'browser',
        'version',
        'system',
        'activity_date',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'datetime',
        ];
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForClientAccount(Builder $query, int $ownerClientId): Builder
    {
        return $query
            ->where('causer_type', Client::class)
            ->whereIn('causer_id', Client::query()
                ->select('id')
                ->where('id', $ownerClientId)
                ->orWhere('parent_id', $ownerClientId));
    }

    public function scopeForCauserType(Builder $query, string $causerType): Builder
    {
        return $query->where('causer_type', $causerType);
    }
}
