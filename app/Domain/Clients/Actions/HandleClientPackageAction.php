<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Services\PackageService;
use App\Domain\Packages\Models\Package;

class HandleClientPackageAction
{
    public function __construct(private PackageService $service) {}

    public function execute(Client $client, ?int $packageId): void
    {
        if (!$packageId) {
            return;
        }

        $package = Package::findOrFail($packageId);
        $activePackage = $client->clientPackages()->where('is_active', true)->first();

        if (!$activePackage || $activePackage->package_id !== $package->id) {
            $this->service->startPackage($client, $package);
        } else {
            $this->service->renewSubscription($client, $package);
        }
    }
}
