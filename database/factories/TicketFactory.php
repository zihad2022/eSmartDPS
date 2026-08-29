<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Client;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'ticket_number' => fn () => 'TKT'.fake()->unique()->numerify('#####'),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::MEDIUM,
            'admin_notes' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::CLOSED,
        ]);
    }
}
