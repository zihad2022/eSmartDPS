<?php

namespace App\Domain\Clients\Models;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use Illuminate\Database\Eloquent\Model;

class ClientPackage extends Model
{
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Check if the subscription is currently active by date and status.
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active' && ! $this->isExpired();
    }

    /**
     * Check if the subscription has expired.
     */
    public function isExpired(): bool
    {
        return $this->ends_at->isPast();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
