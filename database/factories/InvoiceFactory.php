<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $monthOffset = fake()->unique()->numberBetween(1, 1000);
        $start = now()->subMonths($monthOffset)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return [
            'client_id' => Client::factory(),
            'package_id' => Package::factory(),
            'package_name' => 'Standard Plan',
            'package_description' => 'Standard monthly plan',
            'billing_start' => $start,
            'billing_end' => $end,
            'due_date' => $start->copy()->addDays(7),
            'invoice_number' => fn () => 'INV'.fake()->unique()->numerify('#####'),
            'invoice_amount' => 1000,
            'status' => InvoiceStatus::UNPAID,
            'paid_at' => null,
            'payment_reference' => null,
            'payment_id' => null,
            'trx_id' => null,
            'payment_method' => null,
            'wallet_address' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::PAID,
            'paid_at' => now(),
            'payment_id' => generate_payment_id(),
            'trx_id' => fake()->bothify('TRX-#####-????'),
            'payment_method' => PaymentMethod::ONLINE,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::REFUNDED,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::CANCELLED,
        ]);
    }
}
