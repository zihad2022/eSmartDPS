<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Client extends Authenticatable
{
    use HasFactory;
    use Notifiable;

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
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        return $this->role === 'super-admin'
            || in_array($this->role, (array) $roles, true);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function clientPackages(): HasMany
    {
        return $this->hasMany(ClientPackage::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->clientPackages();
    }

    public function latestClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)->latestOfMany('ends_at');
    }

    public function activeClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)
            ->ofMany(['ends_at' => 'max'], fn (Builder $query) => $query->active());
    }

    public function activePaidClientPackage(): HasOne
    {
        return $this->hasOne(ClientPackage::class)
            ->ofMany(['ends_at' => 'max'], fn (Builder $query) => $query->active()->paid());
    }

    public function expiredTrialPackages(): HasMany
    {
        return $this->hasMany(ClientPackage::class)
            ->trial()
            ->expired()
            ->where('is_active', true)
            ->where('status', ClientPackage::STATUS_ACTIVE);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(ClientSetting::class);
    }

    public function currentSubscription(): ?ClientPackage
    {
        return $this->activeClientPackage()->with('package')->first();
    }

    private function subscriptionOwner(): self
    {
        return $this->parent_id ? ($this->parent ?? $this) : $this;
    }

    private function hasCapacity(string $relation, string $limitField): bool
    {
        $owner = $this->subscriptionOwner();
        $subscription = $owner->currentSubscription();

        if (! $subscription?->package) {
            return false;
        }

        $limit = $subscription->package->{$limitField};

        if ($limit === null || (int) $limit === 0) {
            return true;
        }

        $currentCount = $relation === 'accountUsers'
            ? $owner->accountUserCount()
            : $owner->{$relation}()->count();

        return $currentCount < (int) $limit;
    }

    public function canAddUser(): bool
    {
        return $this->hasCapacity('accountUsers', 'user_limit');
    }

    public function canAddMember(): bool
    {
        return $this->hasCapacity('members', 'member_limit');
    }

    public function canAddProject(): bool
    {
        return $this->hasCapacity('projects', 'project_limit');
    }

    public function accountUserCount(): int
    {
        $owner = $this->subscriptionOwner();

        return 1 + $owner->children()->count();
    }

    public function users(): Collection
    {
        $owner = $this->subscriptionOwner();

        return new Collection([$owner, ...$owner->children()->get()->all()]);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    public function scopeParents(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeActiveParents(Builder $query): Builder
    {
        return $query->active()->parents();
    }

    public function scopeInactiveParents(Builder $query): Builder
    {
        return $query->inactive()->parents();
    }

    public function scopeActiveChildren(Builder $query): Builder
    {
        return $query->active()->children();
    }

    public function scopeInactiveChildren(Builder $query): Builder
    {
        return $query->inactive()->children();
    }

    public function scopeFilterBySearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($search): void {
            foreach (['first_name', 'last_name', 'user_id', 'email', 'phone', 'nid_number', 'division', 'district', 'address', 'postal_code'] as $field) {
                $query->orWhere($field, 'like', "%{$search}%");
            }
        });
    }

    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            'active' => $query->active(),
            'inactive' => $query->inactive(),
            default => $query,
        };
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim("{$this->first_name} {$this->last_name}"));
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->profile_photo ? Storage::url($this->profile_photo) : null);
    }

    protected function nidCardFrontUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->nid_card_front ? Storage::url($this->nid_card_front) : null);
    }

    protected function nidCardBackUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->nid_card_back ? Storage::url($this->nid_card_back) : null);
    }
}
