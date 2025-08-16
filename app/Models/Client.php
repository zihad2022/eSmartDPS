<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Client extends Authenticatable
{
    use HasFactory;

    /*--------------------------------
    | MASS ASSIGNABLE FIELDS
    |--------------------------------*/
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

    /*--------------------------------
    | HIDDEN FIELDS
    |--------------------------------*/
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*--------------------------------
    | ATTRIBUTE CASTS
    |--------------------------------*/
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed',
    ];

    /*--------------------------------
    | RELATIONSHIPS
    |--------------------------------*/

    // A client can have child clients (multi-level accounts)
    public function children(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    // A client can have multiple members (users under them)
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    // A client can have multiple projects
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // Payments linked through members
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Member::class);
    }

    // All packages assigned to this client
    public function clientPackages(): HasMany
    {
        return $this->hasMany(ClientPackage::class);
    }

    // Latest package ever assigned (trial or paid)
    public function latestClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)->latestOfMany();
    }

    // Currently active package (trial or paid)
    public function activeClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)
            ->where('is_active', true)
            ->where('ends_at', '>', now());
    }

    // Currently active trial package
    public function activeTrialClientPackage(): HasOne
    {
        return $this->activeClientPackage()->where('is_trial', true);
    }

    // Currently active paid package
    public function activePaidClientPackage(): HasOne
    {
        return $this->activeClientPackage()->where('is_trial', false);
    }

    // Client-specific settings
    public function settings(): HasOne
    {
        return $this->hasOne(ClientSetting::class);
    }

    /*--------------------------------
    | BUSINESS LOGIC METHODS
    |--------------------------------*/

    /**
     * Check if the client add more users based on package limit.
     */
    public function canAddChild(): bool
    {
        // Get the latest package (with related package details)
        $lastPackage = $this->latestClientPackage()->with('package')->first();

        if (! $lastPackage || ! $lastPackage->package) {
            return false; // No package assigned
        }

        // Compare current user count with package user limit
        return $this->children()->count() < $lastPackage->package->user_limit;
    }

    /**
     * Check if the client can add more members based on package limit.
     */
    public function canAddMember(): bool
    {
        // Get the latest package (with related package details)
        $lastPackage = $this->latestClientPackage()->with('package')->first();

        if (! $lastPackage || ! $lastPackage->package) {
            return false; // No package assigned
        }

        // Compare current member count with package member limit
        return $this->members()->count() < $lastPackage->package->member_limit;
    }

    /**
     * Check if the client add more projects based on package limit.
     */
    public function canAddProject(): bool
    {
        // Get the latest package (with related package details)
        $lastPackage = $this->latestClientPackage()->with('package')->first();

        if (! $lastPackage || ! $lastPackage->package) {
            return false; // No package assigned
        }

        // Compare current project count with package project limit
        return $this->projects()->count() < $lastPackage->package->project_limit;
    }

    /*--------------------------------
    | QUERY SCOPES
    |--------------------------------*/

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /*--------------------------------
    | ACCESSORS (Computed Attributes)
    |--------------------------------*/

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->profile_photo ? Storage::url($this->profile_photo) : null
        );
    }

    protected function nidCardFrontUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->nid_card_front ? Storage::url($this->nid_card_front) : null
        );
    }

    protected function nidCardBackUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->nid_card_back ? Storage::url($this->nid_card_back) : null
        );
    }
}
