<?php

namespace Database\Factories;

use App\Domain\Clients\Models\Client;
use App\Models\LedgerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LedgerCategory>
 */
class LedgerCategoryFactory extends Factory
{
    protected $model = LedgerCategory::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
        ];
    }
}
