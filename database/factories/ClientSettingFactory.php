<?php

namespace Database\Factories;

use App\Domain\Clients\Models\Client;
use App\Models\ClientSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientSetting>
 */
class ClientSettingFactory extends Factory
{
    protected $model = ClientSetting::class;

    public function definition(): array
    {
        return [
            'client_id' => fn () => Client::withoutEvents(fn () => Client::factory()->create()->id),
            'organization_name' => fake()->company(),
            'short_name' => fake()->word(),
            'contact_email' => fake()->safeEmail(),
            'contact_phone' => fake()->numerify('017########'),
            'address' => fake()->address(),
            'currency' => 'BDT',
            'share_price' => 1000,
            'minimum_shares' => 1,
            'maximum_shares' => 100,
            'share_transfer_fee' => 50,
            'allow_partial_shares' => false,
            'payment_due_date' => 10,
            'late_payment_fee' => 100,
        ];
    }
}
