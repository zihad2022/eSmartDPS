<?php

namespace App\Actions\Client\Auth;

use App\Domain\Packages\Models\Package;
use App\Models\AdminSetting;

class GetRegistrationDataAction
{
    public function execute(?int $packageId = null): array
    {
        $package = $packageId ? Package::find($packageId) : Package::first();
        $settings = AdminSetting::first();

        return [
            'package' => $package,
            'settings' => $settings,
        ];
    }
}
