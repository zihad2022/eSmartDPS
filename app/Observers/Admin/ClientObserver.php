<?php

namespace App\Observers\Admin;

use App\Models\Client;
use App\Models\ClientSetting;
use App\Services\ActivityLogger;

class ClientObserver
{
    public function creating(Client $client)
    {
        // Generate unique user ID only for parent clients
        if (is_null($client->parent_id)) {
            $client->user_id = generate_client_user_id();
        }
    }

    public function created(Client $client)
    {
        // Determine label based on parent_id
        $label = is_null($client->parent_id) ? 'Client' : 'User';

        ActivityLogger::log("{$label} '{$client->first_name} {$client->last_name}' was created.");

        // Only create settings for parent clients
        if (is_null($client->parent_id)) {
            ClientSetting::create([
                'client_id' => $client->id,
            ]);
        }
    }

    public function updated(Client $client)
    {
        $label = is_null($client->parent_id) ? 'Client' : 'User';
        ActivityLogger::log("{$label} '{$client->first_name} {$client->last_name}' was updated.");
    }

    public function deleted(Client $client)
    {
        $label = is_null($client->parent_id) ? 'Client' : 'User';
        ActivityLogger::log("{$label} '{$client->first_name} {$client->last_name}' was deleted.");

        // Remove associated client settings only for parent clients
        if (is_null($client->parent_id)) {
            ClientSetting::where('client_id', $client->id)->delete();
        }
    }
}
