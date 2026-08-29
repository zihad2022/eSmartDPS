<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Http\Request;

class StartPaidSubscriptionController extends Controller
{
    private PackageService $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    /**
     * Handle starting a paid subscription for a client.
     */
    public function __invoke(Request $request, Package $package)
    {
        $client = Client::findOrFail(owner_client_id());

        $activePackage = $client->activeClientPackage?->package;

        // -----------------------------
        // 1. Check downgrade constraints
        // -----------------------------
        if ($activePackage) {
            $currentMembers = $client->members()->count();
            $currentUsers = $client->users()->count();
            $currentProjects = $client->projects()->count();

            if ($currentMembers > $package->member_limit) {
                return back()->with('error', 'Cannot switch: the selected package allows fewer members than you currently have.');
            }

            if ($currentUsers > $package->user_limit) {
                return back()->with('error', 'Cannot switch: the selected package allows fewer users than you currently have.');
            }

            if ($currentProjects > $package->project_limit) {
                return back()->with('error', 'Cannot switch: the selected package allows fewer projects than you currently have.');
            }
        }

        // -----------------------------
        // 2. Start paid subscription
        // -----------------------------
        $this->packageService->renewSubscription($client, $package);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return back()->with('success', 'Paid subscription started successfully.');
    }
}
