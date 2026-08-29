<?php

namespace App\Actions\Client\Auth;

use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;

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
            'short_name' => request('short_name'),
            'contact_email' => request('contact_email'),
            'contact_phone' => request('contact_phone'),
            'currency' => 'BDT',
        ]);
    }
}
