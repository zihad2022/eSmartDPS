<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;

final readonly class ClientCreationResult
{
    public function __construct(
        public Client $client,
        public bool $credentialsEmailSent,
    ) {}
}
