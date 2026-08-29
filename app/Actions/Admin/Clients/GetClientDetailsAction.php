<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;

class GetClientDetailsAction
{
    public function __construct(private readonly GuardRootClientAction $guard) {}

    public function execute(Client $client): Client
    {
        $this->guard->execute($client);

        return $client->load([
            'activeClientPackage.package',
            'clientPackages' => fn ($query) => $query->with('package')->latest('starts_at'),
        ]);
    }
}
