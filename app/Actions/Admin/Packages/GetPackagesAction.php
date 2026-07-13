<?php

namespace App\Actions\Admin\Packages;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Domain\Packages\Models\Package;
use App\Enums\Package\BillingCycle;

class GetPackagesAction
{
    public function __construct(
        private readonly GetAdminSettingsAction $getSettings,
    ) {}

    public function execute(?string $search, ?string $status, int $perPage = 10): array
    {
        $packages = Package::query()
            ->when(filled($search), function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->active())
            ->when($status === 'inactive', fn ($query) => $query->inactive())
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $counts = Package::query()
            ->selectRaw('is_active, billing_cycle, COUNT(*) AS aggregate')
            ->groupBy('is_active', 'billing_cycle')
            ->get();

        return [
            'packages' => $packages,
            'activePackages' => $counts->where('is_active', true)->sum('aggregate'),
            'inactivePackages' => $counts->where('is_active', false)->sum('aggregate'),
            'monthlyPackages' => $counts->where('billing_cycle', BillingCycle::MONTHLY)->sum('aggregate'),
            'yearlyPackages' => $counts->where('billing_cycle', BillingCycle::YEARLY)->sum('aggregate'),
            'currency' => $this->getSettings->execute()->currency ?: '$',
        ];
    }
}
