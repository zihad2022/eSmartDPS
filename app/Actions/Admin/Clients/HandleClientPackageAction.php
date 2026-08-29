<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Validation\ValidationException;

class HandleClientPackageAction
{
    public function __construct(private readonly PackageService $service) {}

    public function execute(Client $client, ?int $packageId): void
    {
        if (! $packageId) {
            return;
        }

        $activePackage = $client->clientPackages()
            ->where('is_active', true)
            ->latest('id')
            ->first();

        // Editing a client must never renew or extend an unchanged subscription.
        if ($activePackage && (int) $activePackage->package_id === $packageId) {
            return;
        }

        $package = Package::query()->active()->findOrFail($packageId);
        $result = $this->service->startPackage($client, $package);

        if (is_string($result)) {
            throw ValidationException::withMessages([
                'package_id' => [$result],
            ]);
        }
    }
}
