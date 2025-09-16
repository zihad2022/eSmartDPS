<?php

namespace App\Models;

use App\Enums\Package\BillingCycle;
use App\Enums\Package\DiscountType;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    /**
     * The attributes that are mass assignable.
     * These fields can be filled directly using create() or update().
     */
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

    /**
     * Cast attributes to specific types.
     * - is_active, has_trial → boolean
     * - discount_type, billing_cycle → custom PHP Enums
     */
    protected $casts = [
        'is_active' => 'boolean',
        'has_trial' => 'boolean',
        'discount_type' => DiscountType::class,
        'billing_cycle' => BillingCycle::class,
    ];

    /**
     * Hidden attributes when converting to array/json.
     * We hide `remember_token` (not really used for this model, but safe to hide).
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Query Scope: Get only active packages.
     * Usage: Package::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Query Scope: Get only inactive packages.
     * Usage: Package::inactive()->get();
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Relationship: A package can have many clients.
     */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Relationship: A package can have many client-package records.
     * (Useful if you store package purchase history)
     */
    public function ClientPackages()
    {
        return $this->hasMany(ClientPackage::class);
    }

    /**
     * Accessor: Get final price after applying discount.
     *
     * If `discount_type` is percent → Subtract percentage from price.
     * If `discount_type` is fixed → Subtract fixed amount from price.
     *
     * Usage: $package->final_price
     */
    public function getFinalPriceAttribute()
    {
        // Check if discount exists
        if ($this->discount_value !== null && $this->discount_value !== 0) {

            // Percentage-based discount
            if ($this->discount_type === DiscountType::PERCENT) {
                return $this->price - ($this->price * $this->discount_value / 100);
            }
            // Fixed amount discount
            else {
                return $this->price - $this->discount_value;
            }
        }

        // If no discount, return original price
        return $this->price;
    }

     // Calculate discount amount
     public function getDiscountAmountAttribute(): float
     {
         if ($this->discount_value <= 0) {
             return 0;
         }
 
         return match ($this->discount_type) {
             \App\Enums\Package\DiscountType::FIXED => $this->discount_value,
             \App\Enums\Package\DiscountType::PERCENT => ($this->price * $this->discount_value) / 100,
             default => 0,
         };
     }
 
     // Calculate total after discount
     public function getTotalAfterDiscountAttribute(): float
     {
         return max(0, $this->price - $this->discount_amount);
     }
}
