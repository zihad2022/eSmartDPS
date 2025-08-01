<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Client extends Authenticatable
{
    protected $fillable = [
        'parent_id',
        'user_id',
        'password',
        'first_name',
        'last_name',
        'profile_photo',
        'email',
        'phone',
        'nid_number',
        'nid_card_front',
        'nid_card_back',
        'division',
        'district',
        'address',
        'postal_code',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed',
    ];

    // write function for role
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $role): bool
    {
        return in_array($this->role, $role);
    }

    public function parent(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Member::class);
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->profile_photo ? Storage::url($this->profile_photo) : null,
        );
    }

    protected function nidCardFrontUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->nid_card_front ? Storage::url($this->nid_card_front) : null,
        );
    }

    protected function nidCardBackUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->nid_card_back ? Storage::url($this->nid_card_back) : null,
        );
    }

    public function clientPakage()
    {
        return $this->hasOne(ClientPakage::class)->latestOfMany();
    }

    // 1. Active Free Trial pakage
    public function activeTrialClientPakage(): HasOne
    {
        return $this->hasOne(ClientPakage::class)
            ->where('is_active', true)
            ->where('is_trial', true)
            ->where('ends_at', '>', now());
    }

    // 2. Active Paid pakage (not trial)
    public function activePaidClientPakage(): HasOne
    {
        return $this->hasOne(ClientPakage::class)
            ->where('is_active', true)
            ->where('is_trial', false)
            ->where('ends_at', '>', now());
    }

    public function lastClientPakage(): HasOne
    {
        return $this->hasOne(ClientPakage::class)->latestOfMany();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            $client->user_id = generate_client_user_id();
        });
    }
}
