<?php

namespace Database\Factories;

use App\Domain\Packages\Models\Package;
use App\Enums\Package\BillingCycle;
use App\Enums\Package\DiscountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Packages\Models\Package>
 */
class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        $name = fake()->unique()->word().' Plan';

        return [
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'description' => fake()->sentence(),
            'price' => 1000,
            'discount_value' => 0,
            'discount_type' => null,
            'billing_cycle' => BillingCycle::MONTHLY,
            'member_limit' => 100,
            'user_limit' => 5,
            'project_limit' => 10,
            'is_active' => true,
            'has_trial' => false,
            'trial_days' => 0,
            'features' => [
                'members' => true,
                'projects' => true,
                'ledgers' => true,
                'tickets' => true,
                'sms' => false,
            ],
        ];
    }

    public function trial(int $days = 14): static
    {
        return $this->state(fn (array $attributes) => [
            'has_trial' => true,
            'trial_days' => $days,
            'price' => 0,
        ]);
    }

    public function yearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_cycle' => BillingCycle::YEARLY,
            'price' => 10000,
        ]);
    }

    public function withFixedDiscount(int $discount = 200): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_type' => DiscountType::FIXED,
            'discount_value' => $discount,
        ]);
    }

    public function withPercentDiscount(int $percent = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_type' => DiscountType::PERCENT,
            'discount_value' => $percent,
        ]);
    }
}
