<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Packages\Models\Package;
use Illuminate\Support\Facades\Cache;

class GetActivePackagesAction
{
    public function execute()
    {
        return Cache::remember('packages.active.simple', 300, function () {
            return Package::select('id', 'name')->active()->get();
        });
    }
}
