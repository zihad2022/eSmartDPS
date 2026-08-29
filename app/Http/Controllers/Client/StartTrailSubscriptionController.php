<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StartTrailSubscriptionController extends Controller
{
    public function __construct(private readonly PackageService $packageService) {}

    public function __invoke(Request $request, Package $package): RedirectResponse
    {
        $client = Client::findOrFail(owner_client_id());

        if ($error = $this->packageService->validatePackageSwitch($client, $package)) {
            return back()->with('error', $error);
        }

        $result = $this->packageService->startPackage($client, $package);

        if ($result !== true) {
            return back()->with('error', is_string($result) ? $result : 'You have already used the trial for this package.');
        }

        return back()->with('success', "You have successfully started the trial for the '{$package->name}' package. Enjoy your trial period!");
    }
}
