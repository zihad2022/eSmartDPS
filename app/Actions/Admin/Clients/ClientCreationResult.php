<?php

namespace App\Actions\Admin\Clients;

use App\Domain\Clients\Models\Client;

final readonly class ClientCreationResult
{
    public function __construct(
        public Client $client,
        public bool $credentialsEmailSent,
    ) {}
}
