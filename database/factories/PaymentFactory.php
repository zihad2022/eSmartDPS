<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payment_id' => fn () => 'PAY'.fake()->unique()->numerify('#####'),
            'client_id' => Client::factory(),
            'member_id' => Member::factory(),
            'amount' => 500,
            'payment_method' => PaymentMethod::ONLINE,
            'transaction_id' => fake()->bothify('TXN-#####-????'),
            'reference_number' => fake()->numerify('REF-######'),
            'status' => PaymentStatus::PAID->value,
            'paid_at' => now(),
            'due_date' => now()->startOfMonth()->addDays(10),
            'meta' => null,
        ];
    }

    public function due(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::DUE->value,
            'paid_at' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::PENDING->value,
            'paid_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::CANCELLED->value,
            'paid_at' => null,
        ]);
    }
}
