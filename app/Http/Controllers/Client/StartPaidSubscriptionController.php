<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StartPaidSubscriptionController extends Controller
{
    public function __construct(private readonly PackageService $packageService) {}

    /**
     * Handle starting a paid subscription for a client.
     */
    public function __invoke(Request $request, Package $package): RedirectResponse
    {
        $client = Client::findOrFail(owner_client_id());

        if ($error = $this->packageService->validatePackageSwitch($client, $package)) {
            return back()->with('error', $error);
        }

        $this->packageService->renewSubscription($client, $package);

        return back()->with('success', 'Paid subscription started successfully.');
    }
}
