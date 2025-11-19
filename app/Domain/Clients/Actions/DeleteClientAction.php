<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;

class DeleteClientAction
{
    public function execute(Client $client): void
    {
        $client->delete();
    }
}
