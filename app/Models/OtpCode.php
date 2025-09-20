<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OtpCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'userable_id', 'userable_type', 'phone', 'otp', 'expires_at', 'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function userable()
    {
        return $this->morphTo();
    }

    // Check if OTP is expired
    public function isExpired(): bool
    {
        return $this->expires_at ? $this->expires_at->lt(Carbon::now()) : true;
    }
}
