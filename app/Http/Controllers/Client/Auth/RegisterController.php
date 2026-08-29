<?php

namespace App\Http\Controllers\Client\Auth;

use App\Actions\Client\Auth\CreateClientAction;
use App\Actions\Client\Auth\CreateClientInvoiceAction;
use App\Actions\Client\Auth\GetRegistrationDataAction;
use App\Actions\Client\Auth\SendClientCredentialsAction;
use App\Actions\Client\Auth\StartClientPackageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientRequest;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Display registration page.
     */
    public function create(GetRegistrationDataAction $action): View
    {
        return view('client.auth.register', $action->execute(request('package')));
    }

    /**
     * Register a new client and initialize subscription.
     */
    public function store(
        ClientRequest $request,
        CreateClientAction $createClient,
        StartClientPackageAction $startPackage,
        CreateClientInvoiceAction $createInvoice,
        SendClientCredentialsAction $sendCredentials
    ): RedirectResponse {

        Log::info('Client registration initiated');

        // Create client
        $client = $createClient->execute($request->only([
            'first_name', 'last_name', 'email', 'phone', 'password',
        ]));

        // Load package with a single DB hit
        $package = Package::findOrFail($request->package_id);

        // Start subscription package
        $startPackage->execute($client, $package);

        // Create invoice only for paid packages
        if (! $package->has_trial || $package->trial_days === 0) {
            $createInvoice->execute($client, $package);
        }

        // Send login credentials
        $sendCredentials->execute($client, $request->password);

        return redirect()->route('client.auth.success', [
            'id' => $package->id,
        ]);
    }

    /**
     * Registration success screen.
     */
    public function success(int $id): View
    {
        return view('client.auth.success', [
            'package' => Package::findOrFail($id),
        ]);
    }
}
