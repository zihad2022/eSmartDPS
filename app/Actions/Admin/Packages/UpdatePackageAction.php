<?php

namespace App\Actions\Admin\Packages;

use App\Domain\Packages\Models\Package;
use Illuminate\Support\Facades\Cache;

class UpdatePackageAction
{
    public function execute(Package $package, array $data): Package
    {
        $package->update($data);
        Cache::forget('packages.active.simple');

        return $package->refresh();
    }
}
