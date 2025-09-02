<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\ClientSetting;
use App\Services\ActivityLogger;

class ClientObserver
{
    public function creating(Client $client)
    {
        $client->user_id = generate_client_user_id();
    }

    public function created(Client $client)
    {
        // Log client creation activity
        ActivityLogger::log("Client '{$client->first_name} {$client->last_name}' was created.");
    
        // Only create settings if this client has no parent (it's a parent client)
        if (is_null($client->parent_id)) {
            ClientSetting::create([
                'client_id' => $client->id,
            ]);
        }
    }

    public function updated(Client $client)
    {
        ActivityLogger::log("Client '{$client->first_name} {$client->last_name}' was updated.");
    }

    public function deleted(Client $client)
    {
        // Log client deletion
        ActivityLogger::log("Client '{$client->first_name} {$client->last_name}' was deleted.");

        // Remove associated client settings
        ClientSetting::where('client_id', $client->id)->delete();
    }
}
