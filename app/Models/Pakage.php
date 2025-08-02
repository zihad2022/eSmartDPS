<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pakage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_value',
        'discount_type',
        'billing_cycle',
        'member_limit',
        'user_limit',
        'project_limit',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_trial' => 'boolean',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function clientPakages()
    {
        return $this->hasMany(ClientPakage::class);
    }
}
