<?php

namespace App\Actions\Client\Auth;

use App\Models\AdminSetting;
use App\Domain\Packages\Models\Package;

class GetRegistrationDataAction
{
    public function execute(int $packageId): array
    {
        $package = Package::findOrFail($packageId);
        $settings = AdminSetting::first();

        return [
            'package' => $package,
            'settings' => $settings
        ];
    }
}
