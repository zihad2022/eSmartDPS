<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Clients\Actions\CreateClientAction;
use App\Domain\Clients\Actions\DeleteClientAction;
use App\Domain\Clients\Actions\GetClientsAction;
use App\Domain\Clients\Actions\UpdateClientAction;
use App\Domain\Clients\DTOs\ClientData;
use App\Domain\Clients\Models\Client;
use App\Domain\Clients\Services\PackageService;
use App\Domain\Packages\Models\Package;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request, GetClientsAction $getClientsAction): View
    {
        try {
            $clients = $getClientsAction->execute(
                search: $request->get('search'),
                status: $request->get('status')
            );

            return view('admin.client.index', [
                'clients' => $clients,
                'search'  => $request->get('search'),
                ...$getClientsAction->getStats(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to load clients', ['error' => $e->getMessage()]);

            return view('admin.client.index', [
                'clients' => collect(),
                'search' => null,
                'totalClients' => 0,
                'activeClients' => 0,
                'inactiveClients' => 0,
            ])->withErrors(['error' => 'Failed to load clients.']);
        }
    }

    public function create(): View
    {
        return view('admin.client.form', [
            'packages' => $this->getActivePackages(),
            'user_id'  => generate_client_user_id(),
        ]);
    }

    public function store(
        ClientRequest $request,
        CreateClientAction $createClientAction,
        PackageService $packageService
    ): RedirectResponse {

        $client = $createClientAction->execute(
            $this->makeClientData($request)
        );

        $this->handleClientPackage($client, $request->package_id, $packageService);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client): View
    {
        return view('admin.client.show', compact('client'));
    }

    /* ============================================================
       EDIT
    ============================================================ */
    public function edit(Client $client): View
    {
        $this->guardRootClient($client);

        return view('admin.client.form', [
            'client'   => $client,
            'packages' => $this->getActivePackages(),
        ]);
    }

    /* ============================================================
       UPDATE
    ============================================================ */
    public function update(
        Client $client,
        ClientRequest $request,
        UpdateClientAction $updateClientAction,
        PackageService $packageService
    ): RedirectResponse {

        $this->guardRootClient($client);

        $updateClientAction->execute(
            $client,
            $this->makeClientData($request, $client)
        );

        $this->handleClientPackage($client, $request->package_id, $packageService);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }


    /* ============================================================
       DESTROY
    ============================================================ */
    public function destroy(
        Client $client,
        DeleteClientAction $deleteClientAction
    ): RedirectResponse {

        $this->guardRootClient($client);

        try {
            $deleteClientAction->execute($client);

            return redirect()
                ->route('admin.clients.index')
                ->with('success', 'Client deleted successfully.');

        } catch (\Throwable $e) {
            Log::error('Delete client failed', [
                'id' => $client->id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to delete client.']);
        }
    }

    /* ============================================================
       PRIVATE UTILITIES
    ============================================================ */

    private function getActivePackages()
    {
        return Cache::remember('packages.active.simple', 300, function () {
            return Package::select('id', 'name')->active()->get();
        });
    }

    private function makeClientData(ClientRequest $request, ?Client $client = null): ClientData
    {
        $v = $request->validated();

        return new ClientData(
            user_id: $client->user_id ?? generate_client_user_id(),
            first_name: $v['first_name'],
            last_name: $v['last_name'],
            email: $v['email'],
            phone: $v['phone'] ?? null,
            division: $v['division'] ?? null,
            district: $v['district'] ?? null,
            address: $v['address'] ?? null,
            postal_code: $v['postal_code'] ?? null,
            nid_number: $v['nid_number'] ?? null,
            nid_card_front: $request->file('nid_card_front'),
            nid_card_back: $request->file('nid_card_back'),
            profile_photo: $request->file('profile_photo'),
            password: $v['password'] ?? null,
            package_id: $v['package_id'] ?? null,
        );
    }

    /**
     * Handle package start/renew during create or update.
     */
    private function handleClientPackage(Client $client, ?int $packageId, PackageService $service): void
    {
        if (!$packageId) {
            return;
        }

        $package = Package::findOrFail($packageId);
        $activePackage = $client->clientPackages()->where('is_active', true)->first();

        if (!$activePackage || $activePackage->package_id !== $package->id) {
            $service->startPackage($client, $package);
        } else {
            $service->renewSubscription($client, $package);
        }
    }

    private function guardRootClient(Client $client): void
    {
        abort_if($client->parent_id, 403);
    }
}
