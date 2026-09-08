<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'parent_id' => null,
            'user_id' => fn () => 'UID'.fake()->unique()->numerify('######'),
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
            'profile_photo' => null,
            'remember_token' => null,
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
