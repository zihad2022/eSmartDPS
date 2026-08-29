<?php

namespace App\Domain\Packages\Models;

use App\Concerns\HasSlug;
use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientPackage;
use App\Domain\Invoices\Models\Invoice;
use App\Enums\Package\BillingCycle;
use App\Enums\Package\DiscountType;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_value',
        'discount_type',
        'billing_cycle',
        'member_limit',
        'user_limit',
        'project_limit',
        'is_active',
        'has_trial',
        'trial_days',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'discount_value' => 'integer',
            'member_limit' => 'integer',
            'user_limit' => 'integer',
            'project_limit' => 'integer',
            'trial_days' => 'integer',
            'is_active' => 'boolean',
            'has_trial' => 'boolean',
            'discount_type' => DiscountType::class,
            'billing_cycle' => BillingCycle::class,
            'features' => 'array',
        ];
    }

    protected function sluggable(): string
    {
        return 'name';
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ClientPackage::class);
    }

    public function clientPackages(): HasMany
    {
        return $this->subscriptions();
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_packages')
            ->withPivot(['starts_at', 'ends_at', 'is_trial', 'is_active', 'status'])
            ->withTimestamps();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function getDiscountAmountAttribute(): int
    {
        $discount = max(0, (int) $this->discount_value);

        if ($discount === 0 || ! $this->discount_type) {
            return 0;
        }

        return match ($this->discount_type) {
            DiscountType::FIXED => min((int) $this->price, $discount),
            DiscountType::PERCENT => (int) round(((int) $this->price * min(100, $discount)) / 100),
        };
    }

    public function getFinalPriceAttribute(): int
    {
        return max(0, (int) $this->price - $this->discount_amount);
    }

    public function getTotalAfterDiscountAttribute(): int
    {
        return $this->final_price;
    }

    public function hasFeature(string $featureSlug): bool
    {
        return (bool) data_get($this->features ?? [], $featureSlug, false);
    }

    public function billingEndDate(CarbonInterface|string $startDate): Carbon
    {
        $start = Carbon::parse($startDate);

        return match ($this->billing_cycle) {
            BillingCycle::YEARLY => $start->copy()->addYearNoOverflow(),
            default => $start->copy()->addMonthNoOverflow(),
        };
    }
}
