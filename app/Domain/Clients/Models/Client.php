<?php

namespace App\Domain\Clients\Models;

use App\Models\Member;
use App\Models\Project;
use App\Models\Payment;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Clients\Models\ClientPackage;
use App\Models\ClientSetting;
use Illuminate\Database\Eloquent\Builder;
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
        'created_at'        => 'datetime',
        'password'          => 'hashed',
    ];

    /*--------------------------------
    | RELATIONSHIPS
    --------------------------------*/
    public function parent()
    {
        return $this->belongsTo(Client::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Member::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function clientPackages(): HasMany
    {
        return $this->hasMany(ClientPackage::class);
    }

    public function latestClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)->latestOfMany();
    }

    public function activeClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)
            ->where('is_active', true)
            ->where('ends_at', '>', now());
    }

    public function activePaidClientPackage(): HasOne
    {
        return $this->activeClientPackage()->where('is_trial', false);
    }

    public function expiredTrialPackages(): HasMany
    {
        return $this->hasMany(ClientPackage::class)
            ->where('is_trial', true)
            ->where('is_active', true)
            ->where('ends_at', '<=', now());
    }

    public function settings(): HasOne
    {
        return $this->hasOne(ClientSetting::class);
    }

    /*--------------------------------
    | BUSINESS LOGIC
    --------------------------------*/
    private function getLastPackage(): ?ClientPackage
    {
        return $this->latestClientPackage()->with('package')->first();
    }

    private function hasCapacity(string $relation, string $limitField): bool
    {
        $lastPackage = $this->getLastPackage();

        // If no package or no package found → do not allow
        if (!$lastPackage || !$lastPackage->package) {
            return false;
        }

        // Actual limit value from package
        $limit = $lastPackage->package->{$limitField};

        // If limit is NULL or 0 → unlimited
        if (is_null($limit) || $limit == 0) {
            return true;
        }

        // Check count
        return $this->{$relation}()->count() < $limit;
    }


    public function canAddUser(): bool
    {
        return $this->hasCapacity('users', 'user_limit');
    }

    public function canAddMember(): bool
    {
        return $this->hasCapacity('members', 'member_limit');
    }

    public function canAddProject(): bool
    {
        return $this->hasCapacity('projects', 'project_limit');
    }

    public function users()
    {
        if (is_null($this->parent_id)) return collect([$this])->merge($this->children);
        $parent = $this->parent;
        $siblings = $parent ? $parent->children : collect();
        return collect([$parent])->merge($siblings);
    }

    /*--------------------------------
    | SCOPES
    --------------------------------*/
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeActiveParents($query)
    {
        return $query->active()->parents();
    }

    public function scopeInactiveParents($query)
    {
        return $query->inactive()->parents();
    }

    public function scopeActiveChildren($query)
    {
        return $query->active()->children();
    }

    public function scopeInactiveChildren($query)
    {
        return $query->inactive()->children();
    }

    public function scopeFilterBySearch(Builder $query, ?string $search): Builder
    {
        if (!$search) return $query;
        return $query->where(function ($q) use ($search) {
            $fields = ['first_name', 'last_name', 'user_id', 'email', 'phone', 'nid_number', 'division', 'district', 'address', 'postal_code'];
            foreach ($fields as $field) $q->orWhere($field, 'like', "%{$search}%");
        });
    }

    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        if (!$status) return $query;
        return $query->where('status', $status === 'active');
    }

    /*--------------------------------
    | ACCESSORS
    --------------------------------*/
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn() => $this->profile_photo ? Storage::url($this->profile_photo) : null);
    }

    protected function nidCardFrontUrl(): Attribute
    {
        return Attribute::get(fn() => $this->nid_card_front ? Storage::url($this->nid_card_front) : null);
    }

    protected function nidCardBackUrl(): Attribute
    {
        return Attribute::get(fn() => $this->nid_card_back ? Storage::url($this->nid_card_back) : null);
    }
}
