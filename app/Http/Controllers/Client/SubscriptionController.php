<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use App\Services\PackageService;

class SubscriptionController extends Controller
{
    /**
     * Display the expired subscription page.
     */
    public function expired()
    {
        $clientId = owner_client_id();

        // Get the latest unpaid invoice for this client
        $invoice = Invoice::where('client_id', $clientId)
            ->where('status', '!=', InvoiceStatus::PAID)
            ->first();

        return view('client.subscription.expired', compact('invoice'));
    }

    /**
     * Display all available packages for the client.
     */
    public function packages(PackageService $packageService)
    {
        // -----------------------------
        // 1. Fetch active packages
        // -----------------------------
        // Retrieve all packages that are currently active
        // and available for subscription.
        $packages = Package::active()->get();

        // -----------------------------
        // 2. Load global admin settings
        // -----------------------------
        // Admin settings may include currency, branding,
        // or other configurations needed for the view.
        $settings = AdminSetting::first();

        // -----------------------------
        // 3. Calculate package-switch eligibility for the current client.
        // -----------------------------
        $client = Client::findOrFail(owner_client_id());
        $packageEligibility = $packages->mapWithKeys(function (Package $package) use ($client, $packageService) {
            $issues = $packageService->packageSwitchIssues($client, $package);

            return [$package->id => [
                'eligible' => $issues === [],
                'message' => $issues === [] ? null : $packageService->validatePackageSwitch($client, $package),
                'issues' => $issues,
            ]];
        });

        // -----------------------------
        // 4. Return packages view
        // -----------------------------
        return view('client.subscription.packages', compact('packages', 'settings', 'packageEligibility'));
    }
}
