<?php

namespace App\Observers;

use App\Models\Client;
use App\Services\ActivityLogger;

class ClientObserver
{
    public function creating(Client $client)
    {
        $client->user_id = generate_client_user_id();
    }

    public function created(Client $client)
    {
        ActivityLogger::log("Client '{$client->first_name} {$client->last_name}' was created.");
    }

    public function updated(Client $client)
    {
        ActivityLogger::log("Client '{$client->first_name} {$client->last_name}' was updated.");
    }

    public function deleted(Client $client)
    {
        ActivityLogger::log("Client '{$client->first_name} {$client->last_name}' was deleted.");
    }
}
