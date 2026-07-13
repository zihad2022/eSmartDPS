<?php

namespace App\Actions\Admin\Packages;

use App\Domain\Packages\Models\Package;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class DeletePackageAction
{
    public function execute(Package $package): void
    {
        if ($package->subscriptions()->exists() || $package->invoices()->exists()) {
            throw ValidationException::withMessages([
                'package' => ['This package has subscription or invoice history. Deactivate it instead of deleting it.'],
            ]);
        }

        $package->delete();
        Cache::forget('packages.active.simple');
    }
}
