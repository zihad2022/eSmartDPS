<?php

namespace App\Http\Controllers\Client;

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
    public function renew(PackageService $packageService, $invoiceId = null)
    {
        // -----------------------------
        // 1. If invoice is passed, mark it as PAID
        // -----------------------------
        if ($invoiceId) {
            $invoice = Invoice::findOrFail($invoiceId);
    
            if ($invoice->status !== \App\Enums\InvoiceStatus::PAID) {
                $invoice->update([
                    'status' => \App\Enums\InvoiceStatus::PAID,
                    'paid_at' => now(),
                ]);
            }
        }
    
        // -----------------------------
        // 2. Get authenticated client with packages
        // -----------------------------
        $client = Client::with(['activeClientPackage.package', 'latestClientPackage.package'])
            ->findOrFail(owner_client_id());
    
        // -----------------------------
        // 3. Determine the package to renew
        // -----------------------------
        $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
    
        if (! $clientPackage || ! $clientPackage->package) {
            return redirect()
                ->route('client.subscription.packages')
                ->with('error', 'No active or previous package found. Please choose a package to subscribe.');
        }
    
        // -----------------------------
        // 4. Renew subscription via service
        // -----------------------------
        $packageService->renewSubscription($client, $clientPackage->package);
    
        // -----------------------------
        // 5. Redirect with success message
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
