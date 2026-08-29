<?php

namespace App\Actions\Admin\Packages;

use App\Models\Package;
use Illuminate\Support\Facades\Cache;

class CreatePackageAction
{
    public function execute(array $data): Package
    {
        $package = Package::query()->create($data);
        Cache::forget('packages.active.simple');

        return $package;
    }
}
