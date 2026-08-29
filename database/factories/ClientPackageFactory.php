<?php

namespace Database\Factories;

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Models\ClientPackage;
use App\Domain\Packages\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Clients\Models\ClientPackage>
 */
class ClientPackageFactory extends Factory
{
    protected $model = ClientPackage::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'package_id' => Package::factory(),
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'is_trial' => false,
            'is_active' => true,
            'status' => ClientPackage::STATUS_ACTIVE,
        ];
    }

    public function trial(int $days = 14): static
    {
        return $this->state(fn (array $attributes) => [
            'is_trial' => true,
            'starts_at' => now(),
            'ends_at' => now()->addDays($days),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'status' => ClientPackage::STATUS_EXPIRED,
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
        ]);
    }
}
