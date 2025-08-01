<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create ticket for every clients
        $clients = Client::all();

        foreach ($clients as $client) {
            Ticket::create([
                'client_id' => $client->id,
                'ticket_number' => (new TicketService())->generateTicketNumber(),
                'subject' => 'New Ticket',
                'message' => 'This is a new ticket',
                'status' => rand(1, 4),
                'priority' => rand(1, 3),
            ]);
        }
    }
}
