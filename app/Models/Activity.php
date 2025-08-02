<?php

namespace App\Models;

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

    protected $casts = [
        'activity_date' => 'datetime',
    ];

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }
}
