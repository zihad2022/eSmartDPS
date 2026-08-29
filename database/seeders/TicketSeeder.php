<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::whereNull('parent_id')->get();
        $admin = Admin::first(); // Get first admin to use for replies

        if (! $admin) {
            $this->command->warn('No admin found. Please seed an admin first.');

            return;
        }

        foreach ($clients as $client) {
            // Create a ticket for each client
            $ticket = Ticket::create([
                'client_id' => $client->id,
                'ticket_number' => generate_ticket_number(),
                'subject' => 'New Ticket',
                'message' => 'This is a new ticket',
                'status' => rand(1, 4),
                'priority' => rand(1, 3),
            ]);

            // Add a client reply
            TicketReply::create([
                'ticket_id' => $ticket->id,
                'client_id' => $client->id,
                'message' => 'I need help with something.',
            ]);

            // Add an admin reply
            TicketReply::create([
                'ticket_id' => $ticket->id,
                'admin_id' => $admin->id,
                'message' => 'We have received your request and are working on it.',
            ]);
        }
    }
}
