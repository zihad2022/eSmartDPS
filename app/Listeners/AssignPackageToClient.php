<?php

namespace App\Listeners;

use App\Events\Admin\ClientCreated;
use App\Services\PackageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignPackageToClient
{
    public function __construct(private PackageService $packageService) {}

    public function handle(ClientCreated $event): void
    {
        if ($event->packageId) {
            $package = \App\Models\Package::find($event->packageId);
            $package && $this->packageService->startPackage($event->client, $package);
        }
    }
}