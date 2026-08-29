<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Http\Request;

class StartSubscriptionController extends Controller
{
    private $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $client = Client::findOrFail(owner_client_id());
        $package = Package::findOrFail($request['package_id']);

        $activePackage = $client->activeClientPackage?->package;

        // If the client already has a package, check upgrade/downgrade constraints
        if ($activePackage) {
            // Collect client usage stats
            $currentMembers = $client->members()->count();
            $currentUsers = $client->users()->count();
            $currentProjects = $client->projects()->count();

            // Check if trying to downgrade (limits lower than current usage)
            if ($currentMembers > $package->member_limit) {
                return back()->with('error', 'You cannot downgrade: new package allows fewer members than you currently have.');
            }

            if ($currentUsers > $package->user_limit) {
                return back()->with('error', 'You cannot downgrade: new package allows fewer users than you currently have.');
            }

            if ($currentProjects > $package->project_limit) {
                return back()->with('error', 'You cannot downgrade: new package allows fewer projects than you currently have.');
            }
        }

        // Start the new package
        $this->packageService->startPackage($client, $package);

        return back()->with('success', 'Subscription started successfully.');
    }
}
