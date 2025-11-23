<?php

namespace App\Actions\Client\Auth;

use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Domain\Clients\Services\PackageService;

class StartClientPackageAction
{
    protected PackageService $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    public function execute(Client $client, Package $package): void
    {
        $this->packageService->startPackage($client, $package);

        $client->settings()->update([
            'organization_name' => request('organization_name'),
            'short_name'        => request('short_name'),
            'contact_email'     => request('contact_email'),
            'contact_phone'     => request('contact_phone'),
            'currency'          => 'BDT',
        ]);
    }
}
