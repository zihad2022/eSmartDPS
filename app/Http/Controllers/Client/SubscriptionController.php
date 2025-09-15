<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;

class SubscriptionController extends Controller
{
    /**
     * Display the expired subscription page.
     */
    public function expired()
    {
        // -----------------------------
        // 1. Return expired subscription view
        // -----------------------------
        // Simply display a message informing the client
        // that their subscription has expired.
        return view('client.subscription.expired');
    }

    /**
     * Renew the client's subscription.
     */
    public function renew(PackageService $packageService)
    {
        // -----------------------------
        // 1. Get authenticated client ID
        // -----------------------------
        $clientId = owner_client_id();

        // -----------------------------
        // 2. Load client with package relations
        // -----------------------------
        // Load both active and latest packages to determine
        // which package should be renewed.
        $client = Client::with(['activeClientPackage.package', 'latestClientPackage.package'])
            ->findOrFail($clientId);

        // -----------------------------
        // 3. Determine the package to renew
        // -----------------------------
        // Prefer the currently active package; otherwise,
        // use the latest package the client has.
        $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;

        // If no package exists, abort with 404
        if (! $clientPackage || ! $clientPackage->package) {
            abort(404, 'No package found to renew.');
        }

        // -----------------------------
        // 4. Renew the subscription
        // -----------------------------
        // Call the package service to renew the subscription
        // for the selected package.
        $packageService->renewSubscription($client, $clientPackage->package);

        // -----------------------------
        // 5. Redirect to dashboard with success message
        // -----------------------------
        return redirect()
            ->route('client.dashboard')
            ->with('success', 'Your subscription has been successfully renewed.');
    }

    /**
     * Display all available packages for the client.
     */
    public function packages()
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
        // 3. Return packages view
        // -----------------------------
        // Pass the packages and settings to the view
        // for display to the client.
        return view('client.subscription.packages', compact('packages', 'settings'));
    }
}
