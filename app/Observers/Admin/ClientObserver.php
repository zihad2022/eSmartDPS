<?php

namespace App\Observers\Admin;

use App\Domain\Clients\Models\Client;
use App\Models\ClientSetting;
use App\Services\ActivityLogger;

class ClientObserver
{
    public function creating(Client $client): void
    {
        if (blank($client->user_id)) {
            $client->user_id = generate_client_user_id();
        }
    }

    public function created(Client $client): void
    {
        $label = $client->parent_id === null ? 'Client' : 'User';
        ActivityLogger::log("{$label} '{$client->full_name}' was created.");

        if ($client->parent_id === null) {
            ClientSetting::query()->firstOrCreate(['client_id' => $client->id]);
        }
    }

    public function updated(Client $client): void
    {
        $label = $client->parent_id === null ? 'Client' : 'User';
        ActivityLogger::log("{$label} '{$client->full_name}' was updated.");
    }

    public function deleted(Client $client): void
    {
        $label = $client->parent_id === null ? 'Client' : 'User';
        ActivityLogger::log("{$label} '{$client->full_name}' was deleted.");
    }
}
