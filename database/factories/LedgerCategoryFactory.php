<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\LedgerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LedgerCategory>
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
