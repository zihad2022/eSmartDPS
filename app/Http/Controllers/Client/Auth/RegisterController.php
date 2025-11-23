<?php

namespace App\Http\Controllers\Client\Auth;

use App\Actions\Client\Auth\CreateClientAction;
use App\Actions\Client\Auth\CreateClientInvoiceAction;
use App\Actions\Client\Auth\GetRegistrationDataAction;
use App\Actions\Client\Auth\SendClientCredentialsAction;
use App\Actions\Client\Auth\StartClientPackageAction;
use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Show registration form for a client with selected package.
     */
    public function create(GetRegistrationDataAction $action): View
    {
        $data = $action->execute(request('package'));
        return view('client.auth.register', $data);
    }

    /**
     * Store a new client and start the selected package.
     */
    public function store(
        Request $request,
        CreateClientAction $createClient,
        StartClientPackageAction $startPackage,
        CreateClientInvoiceAction $createInvoice,
        SendClientCredentialsAction $sendCredentials
    ): RedirectResponse {
        // 1. Create client
        $client = $createClient->execute($request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'password',
        ]));

        // 2. Start package and update client settings
        $package = Package::findOrFail($request->package_id);
        $startPackage->execute($client, $package);

        // 3. Create invoice
        $invoice = $createInvoice->execute($client, $package);

        // 4. Send credentials
        $sendCredentials->execute($client, $request->password);

        // 5. Redirect to success page
        return redirect()->route('client.auth.success', [
            'id' => $package->id,
            'invoice' => $invoice->id,
        ]);
    }

    /**
     * Show success page after registration.
     */
    public function success(int $id): View
    {
        $package = Package::findOrFail($id);
        return view('client.auth.success', compact('package'));
    }
}
