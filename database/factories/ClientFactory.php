<?php

namespace Database\Factories;

use App\Domain\Clients\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Clients\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'user_id' => fn () => 'UID' . fake()->unique()->numerify('######'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('017########'),
            'password' => static::$password ??= Hash::make('password'),
            'nid_number' => fake()->numerify('##########'),
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'address' => fake()->address(),
            'postal_code' => '1200',
            'role' => 'super-admin',
            'status' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    public function subUser(Client $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'role' => 'manager',
        ]);
    }
}
