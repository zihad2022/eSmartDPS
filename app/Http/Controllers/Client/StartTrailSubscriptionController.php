<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;

class StartTrailSubscriptionController extends Controller
{
    private $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    public function __invoke(Request $request)
    {
        $client = Client::findOrFail(owner_client_id());
        $package = Package::findOrFail($request['package_id']);

        $activePackage = $client->activeClientPackage?->package;

        // If the client already has a package, check upgrade/downgrade constraints
        if ($activePackage) {
            $currentMembers = $client->members()->count();
            $currentUsers   = $client->users()->count();
            $currentProjects = $client->projects()->count();

            // Prevent downgrading to a package with lower limits than current usage
            if ($currentMembers > $package->member_limit) {
                return back()->with('error', 'Cannot start trial: the selected package allows fewer members than you currently have.');
            }

            if ($currentUsers > $package->user_limit) {
                return back()->with('error', 'Cannot start trial: the selected package allows fewer users than you currently have.');
            }

            if ($currentProjects > $package->project_limit) {
                return back()->with('error', 'Cannot start trial: the selected package allows fewer projects than you currently have.');
            }
        }

        // Start the trial subscription
        $result = $this->packageService->startPackage($client, $package);

        if ($result !== true) {
            // return back()->with('error', $result);
            return back()->with('error', "You have already used the trial for this package.");
        }

        return back()->with('success', "You have successfully started the trial for the '{$package->name}' package. Enjoy your trial period!");
    }
}
