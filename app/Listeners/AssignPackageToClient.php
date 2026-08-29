<?php

namespace App\Listeners;

use App\Events\Admin\ClientCreated;
use App\Models\Package;
use App\Services\PackageService;

class AssignPackageToClient
{
    public function __construct(private PackageService $packageService) {}

    public function handle(ClientCreated $event): void
    {
        if ($event->packageId) {
            $package = Package::find($event->packageId);
            $package && $this->packageService->startPackage($event->client, $package);
        }
    }
}
