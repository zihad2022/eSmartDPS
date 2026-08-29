<?php

namespace Database\Factories;

use App\Domain\Clients\Models\Client;
use App\Enums\Ledger\LedgerType;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ledger>
 */
class LedgerFactory extends Factory
{
    protected $model = Ledger::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'ledger_category_id' => LedgerCategory::factory(),
            'type' => LedgerType::INCOME,
            'description' => fake()->sentence(),
            'amount' => 1500,
            'entry_date' => now()->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LedgerType::EXPENSE,
        ]);
    }
}
