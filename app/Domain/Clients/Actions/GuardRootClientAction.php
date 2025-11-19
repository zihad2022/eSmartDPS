<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;

class GuardRootClientAction
{
    public function execute(Client $client): void
    {
        abort_if($client->parent_id, 403);
    }
}
