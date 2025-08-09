<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

/**
 * Class Client
 *
 * Represents a client account in the system.
 * Clients can have child clients, members, packages, and settings.
 */
class Client extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'parent_id',        // Reference to parent client (for hierarchy)
        'user_id',          // Unique identifier for the client
        'password',         // Hashed password
        'first_name',
        'last_name',
        'profile_photo',    // Path to profile image
        'email',
        'phone',
        'nid_number',       // National ID number
        'nid_card_front',   // Path to NID front image
        'nid_card_back',    // Path to NID back image
        'division',
        'district',
        'address',
        'postal_code',
        'role',             // Role type (admin, manager, etc.)
        'status',           // Active/Inactive status
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed', // Automatically hash when set
    ];

    /*--------------------------------
    | RELATIONSHIPS
    |--------------------------------*/

    /**
     * Get child clients under this client (Hierarchy: Parent → Children).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    /**
     * Alias for children() to avoid confusion (parent() method name was misleading).
     * If you want parent client: use belongsTo instead.
     */
    public function parentClients(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    /**
     * Get members that belong to this client.
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get payments for this client through its members.
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Member::class);
    }

    /**
     * Get latest assigned package for this client.
     */
    public function clientPackage(): HasOne
    {
        return $this->hasOne(ClientPakage::class)->latestOfMany();
    }

    /**
     * Get active trial package for this client.
     */
    public function activeTrialClientPackage(): HasOne
    {
        return $this->hasOne(ClientPakage::class)
            ->where('is_active', true)
            ->where('is_trial', true)
            ->where('ends_at', '>', now());
    }

    /**
     * Get active paid (non-trial) package for this client.
     */
    public function activePaidClientPackage(): HasOne
    {
        return $this->hasOne(ClientPakage::class)
            ->where('is_active', true)
            ->where('is_trial', false)
            ->where('ends_at', '>', now());
    }

    /**
     * Get last package assigned to the client (active or not).
     */
    public function lastClientPackage(): HasOne
    {
        return $this->hasOne(ClientPakage::class)->latestOfMany();
    }

    /**
     * Get settings related to this client.
     */
    public function settings(): HasOne
    {
        return $this->hasOne(ClientSetting::class);
    }

    /*--------------------------------
    | QUERY SCOPES
    |--------------------------------*/

    /**
     * Scope: Only active clients.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Only inactive clients.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope: Only parent clients (no parent_id set).
     */
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /*--------------------------------
    | ACCESSORS
    |--------------------------------*/

    /**
     * Get the full URL for profile photo.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->profile_photo ? Storage::url($this->profile_photo) : null
        );
    }

    /**
     * Get the full URL for NID front photo.
     */
    protected function nidCardFrontUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->nid_card_front ? Storage::url($this->nid_card_front) : null
        );
    }

    /**
     * Get the full URL for NID back photo.
     */
    protected function nidCardBackUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->nid_card_back ? Storage::url($this->nid_card_back) : null
        );
    }
}
