<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;

class GuardRootClientAction
{
    public function execute(Client $client): void
    {
        abort_if($client->parent_id, 403);
    }
}
