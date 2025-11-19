<?php

namespace App\Events\Admin;

use App\Domain\Clients\Models\Client;
use Illuminate\Foundation\Events\Dispatchable;

class ClientCreated
{
    use Dispatchable;

    public function __construct(
        public Client $client,
        public $packageId,
        public $password
    ) {}
}
