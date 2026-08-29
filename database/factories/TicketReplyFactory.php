<?php

namespace Database\Factories;

use App\Domain\Clients\Models\Client;
use App\Models\Admin;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketReply>
 */
class TicketReplyFactory extends Factory
{
    protected $model = TicketReply::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'client_id' => Client::factory(),
            'admin_id' => null,
            'message' => fake()->paragraph(),
            'attachment' => null,
        ];
    }

    public function fromAdmin(Admin $admin): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_id' => $admin->id,
            'client_id' => null,
        ]);
    }
}
