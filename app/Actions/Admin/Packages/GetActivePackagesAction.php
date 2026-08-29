<?php

namespace App\Actions\Admin\Packages;

use App\Models\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GetActivePackagesAction
{
    public function execute(?int $includePackageId = null): Collection
    {
        $packages = Cache::remember('packages.active.simple', 300, fn () => Package::query()->select('id', 'name')->active()->orderBy('name')->get()
        );

        if ($includePackageId && ! $packages->contains('id', $includePackageId)) {
            $current = Package::query()->select('id', 'name')->find($includePackageId);

            if ($current) {
                $packages = $packages->push($current)->sortBy('name')->values();
            }
        }

        return $packages;
    }
}
