<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Clients\Actions\CreateClientAction;
use App\Domain\Clients\Actions\DeleteClientAction;
use App\Domain\Clients\Actions\GetClientsAction;
use App\Domain\Clients\Actions\UpdateClientAction;
use App\Domain\Clients\Actions\PrepareClientDataAction;
use App\Domain\Clients\Actions\HandleClientPackageAction;
use App\Domain\Clients\Actions\GetActivePackagesAction;
use App\Domain\Clients\Actions\GuardRootClientAction;
use App\Domain\Clients\Models\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index(Request $request, GetClientsAction $action): View
    {
        try {
            $clients = $action->execute(
                search: $request->get('search'),
                status: $request->get('status')
            );

            return view('admin.client.index', [
                'clients' => $clients,
                'search'  => $request->get('search'),
                ...$action->getStats(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to load clients', ['error' => $e->getMessage()]);

            return view('admin.client.index', [
                'clients' => collect(),
                'search' => null,
                'totalClients' => 0,
                'activeClients' => 0,
                'inactiveClients' => 0,
            ])->with(['error' => 'Failed to load clients.']);
        }
    }

    public function create(GetActivePackagesAction $action): View
    {
        return view('admin.client.form', [
            'packages' => $action->execute(),
            'user_id'  => generate_client_user_id(),
        ]);
    }

    public function store(
        ClientRequest $request,
        CreateClientAction $create,
        PrepareClientDataAction $prepare,
        HandleClientPackageAction $packageHandler
    ): RedirectResponse {

        $client = $create->execute(
            $prepare->execute($request)
        );

        $packageHandler->execute($client, $request->package_id);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function edit(
        Client $client,
        GuardRootClientAction $guard,
        GetActivePackagesAction $packages
    ): View {

        $guard->execute($client);

        return view('admin.client.form', [
            'client'   => $client,
            'packages' => $packages->execute(),
        ]);
    }

    public function update(
        Client $client,
        ClientRequest $request,
        UpdateClientAction $update,
        PrepareClientDataAction $prepare,
        HandleClientPackageAction $packageHandler,
        GuardRootClientAction $guard
    ): RedirectResponse {

        $guard->execute($client);

        $update->execute(
            $client,
            $prepare->execute($request, $client)
        );

        $packageHandler->execute($client, $request->package_id);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(
        Client $client,
        DeleteClientAction $delete,
        GuardRootClientAction $guard
    ): RedirectResponse {

        $guard->execute($client);

        try {
            $delete->execute($client);
            return redirect()->route('admin.clients.index')
                ->with('success', 'Client deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Delete client failed', [
                'id' => $client->id,
                'error' => $e->getMessage()
            ]);

            return back()->with(['error' => 'Failed to delete client.']);
        }
    }
}
