<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

/**
 * Client Model
 *
 * Represents a registered client in the system.
 * Clients can have children (sub-clients), members, projects, packages, and settings.
 */
class Client extends Authenticatable
{
    use HasFactory;

    /*--------------------------------
    | MASS ASSIGNABLE
    --------------------------------*/
    // Fields that can be mass-assigned (e.g., via create or update)
    protected $fillable = [
        'parent_id',         // For hierarchy (self-relation: child/parent client)
        'user_id',           // Linked user ID (if applicable)
        'password',          // Encrypted password
        'first_name',        // Client's first name
        'last_name',         // Client's last name
        'profile_photo',     // Path to profile photo
        'email',             // Client's email
        'phone',             // Client's phone
        'nid_number',        // National ID number
        'nid_card_front',    // Path to uploaded NID front image
        'nid_card_back',     // Path to uploaded NID back image
        'division',          // Division/State
        'district',          // District/Region
        'address',           // Full address
        'postal_code',       // Postal code
        'role',              // Role in the system (e.g., admin, client)
        'status',            // Active/Inactive
    ];

    /*--------------------------------
    | HIDDEN FIELDS
    --------------------------------*/
    // Hidden from JSON output
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*--------------------------------
    | CASTS
    --------------------------------*/
    // Type casting for attributes
    protected $casts = [
        'email_verified_at' => 'datetime', // Cast email verification timestamp
        'created_at' => 'datetime',        // Cast creation time
        'password' => 'hashed',            // Auto hash password
    ];

    /*--------------------------------
    | RELATIONSHIPS
    --------------------------------*/

    // A client can have many children (sub-clients)
    public function children(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    // A client can belong to a parent client
    public function parent()
    {
        return $this->belongsTo(Client::class, 'parent_id');
    }

    // A client can have many members
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    // A client can have many projects
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // A client has many payments through its members
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Member::class);
    }

    // A client can subscribe to many packages
    public function clientPackages(): HasMany
    {
        return $this->hasMany(ClientPackage::class);
    }

    // Latest package subscribed by the client
    public function latestClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)->latestOfMany();
    }

    // Currently active package (must not be expired and must be marked active)
    public function activeClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)
            ->where('is_active', true)
            ->where('ends_at', '>', now());
    }

    // Active package that is a trial
    public function activeTrialClientPackage(): HasOne
    {
        return $this->activeClientPackage()->where('is_trial', true);
    }

    // Active package that is paid (not a trial)
    public function activePaidClientPackage(): HasOne
    {
        return $this->activeClientPackage()->where('is_trial', false);
    }

    // Each client has one settings row
    public function settings(): HasOne
    {
        return $this->hasOne(ClientSetting::class);
    }

    /*--------------------------------
    | BUSINESS LOGIC
    --------------------------------*/

    /**
     * Helper: Fetch the last subscribed package along with package details.
     */
    private function getLastPackage()
    {
        return $this->latestClientPackage()->with('package')->first();
    }

    /**
     * Check if client can add another child (sub-client)
     * Depends on current package's user limit.
     */
    public function canAddChild(): bool
    {
        $lastPackage = $this->getLastPackage();

        if (! $lastPackage || ! $lastPackage->package) {
            return false;
        }

        $userLimit = $lastPackage->package->user_limit;

        // If user_limit is 0 => unlimited
        if ($userLimit == 0) {
            return true;
        }

        return $this->children()->count() < $userLimit;
    }

    /**
     * Check if client can add another member
     * Depends on current package's member limit.
     */
    public function canAddMember(): bool
    {
        $lastPackage = $this->getLastPackage();

        if (! $lastPackage || ! $lastPackage->package) {
            return false;
        }

        $memberLimit = $lastPackage->package->member_limit;

        // If member_limit is 0 => unlimited
        if ($memberLimit == 0) {
            return true;
        }

        return $this->members()->count() < $memberLimit;
    }

    /**
     * Check if client can add another project
     * Depends on current package's project limit.
     */
    public function canAddProject(): bool
    {
        $lastPackage = $this->getLastPackage();

        if (! $lastPackage || ! $lastPackage->package) {
            return false;
        }

        $projectLimit = $lastPackage->package->project_limit;

        // If project_limit is 0 => unlimited
        if ($projectLimit == 0) {
            return true;
        }

        return $this->projects()->count() < $projectLimit;
    }

    /*--------------------------------
    | SCOPES (query helpers)
    --------------------------------*/

    // Only active clients
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Only inactive clients
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    // Only parent clients (top-level, no parent)
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // Only children (have a parent)
    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    // Active parent clients
    public function scopeActiveParents($q)
    {
        return $q->active()->parents();
    }

    // Inactive parent clients
    public function scopeInactiveParents($q)
    {
        return $q->inactive()->parents();
    }

    // Active children
    public function scopeActiveChildren($q)
    {
        return $q->active()->children();
    }

    // Inactive children
    public function scopeInactiveChildren($q)
    {
        return $q->inactive()->children();
    }

    /*--------------------------------
    | ACCESSORS (computed attributes)
    --------------------------------*/

    // Full URL for profile photo
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->profile_photo ? Storage::url($this->profile_photo) : null);
    }

    // Full URL for NID front image
    protected function nidCardFrontUrl(): Attribute
    {
        return Attribute::get(fn () => $this->nid_card_front ? Storage::url($this->nid_card_front) : null);
    }

    // Full URL for NID back image
    protected function nidCardBackUrl(): Attribute
    {
        return Attribute::get(fn () => $this->nid_card_back ? Storage::url($this->nid_card_back) : null);
    }
}
