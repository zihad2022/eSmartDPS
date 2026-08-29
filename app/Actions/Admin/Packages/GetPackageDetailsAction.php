<?php

namespace App\Actions\Admin\Packages;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Models\Package;

class GetPackageDetailsAction
{
    public function __construct(
        private readonly GetAdminSettingsAction $getSettings,
    ) {}

    public function execute(Package $package): array
    {
        $package->loadCount([
            'subscriptions',
            'invoices',
            'subscriptions as active_subscriptions_count' => fn ($query) => $query->active(),
        ]);

        return [
            'package' => $package,
            'currency' => $this->getSettings->execute()->currency ?: '$',
        ];
    }
}
