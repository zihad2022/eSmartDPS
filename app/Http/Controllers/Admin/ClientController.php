<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Clients\CreateClientWithPackageAction;
use App\Actions\Admin\Clients\GetClientDetailsAction;
use App\Actions\Admin\Clients\UpdateClientWithPackageAction;
use App\Domain\Clients\Actions\DeleteClientAction;
use App\Domain\Clients\Actions\GetActivePackagesAction;
use App\Domain\Clients\Actions\GetClientsAction;
use App\Domain\Clients\Actions\GuardRootClientAction;
use App\Domain\Clients\Actions\PrepareClientDataAction;
use App\Domain\Clients\Models\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request, GetClientsAction $action): View
    {
        return view('admin.client.index', [
            'clients' => $action->execute(
                search: $request->string('search')->toString() ?: null,
                status: $request->string('status')->toString() ?: null,
            ),
            'search' => $request->string('search')->toString(),
            ...$action->getStats(),
        ]);
    }

    public function create(GetActivePackagesAction $action): View
    {
        return view('admin.client.form', [
            'packages' => $action->execute(),
            'user_id' => generate_client_user_id(),
        ]);
    }

    public function store(
        ClientRequest $request,
        PrepareClientDataAction $prepare,
        CreateClientWithPackageAction $create,
    ): RedirectResponse {
        $result = $create->execute($prepare->execute($request));

        $message = $result->credentialsEmailSent
            ? 'Client created successfully and login credentials were emailed.'
            : 'Client created successfully. The credentials email could not be sent; verify the SMTP settings.';

        return redirect()->route('admin.clients.index')
            ->with('success', $message);
    }

    public function show(Client $client, GetClientDetailsAction $action): View
    {
        return view('admin.client.show', ['client' => $action->execute($client)]);
    }

    public function edit(
        Client $client,
        GuardRootClientAction $guard,
        GetActivePackagesAction $packages,
    ): View {
        $guard->execute($client);

        $client->load('activeClientPackage');

        return view('admin.client.form', [
            'client' => $client,
            'packages' => $packages->execute($client->activeClientPackage?->package_id),
        ]);
    }

    public function update(
        Client $client,
        ClientRequest $request,
        PrepareClientDataAction $prepare,
        UpdateClientWithPackageAction $update,
        GuardRootClientAction $guard,
    ): RedirectResponse {
        $guard->execute($client);
        $update->execute($client, $prepare->execute($request, $client));

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(
        Client $client,
        DeleteClientAction $delete,
        GuardRootClientAction $guard,
    ): RedirectResponse {
        $guard->execute($client);

        try {
            $delete->execute($client);
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }
}
