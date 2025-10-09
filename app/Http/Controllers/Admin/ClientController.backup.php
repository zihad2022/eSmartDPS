<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\Client;
use App\Models\Package;
use App\Repositories\Admin\ClientRepository;
use App\Services\Admin\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    // =========================================================
    // Constructor & Dependencies
    // =========================================================
    public function __construct(
        private readonly ClientRepository $clientRepo,
        private readonly ClientService $clientService
    ) {}

    // =========================================================
    // Client Listing
    // =========================================================
    public function index(Request $request): View
    {
        $clients = $this->clientRepo->searchAndFilter(
            $request->get('search'),
            $request->get('status')
        );

        $stats = $this->clientRepo->getStats();

        return view('admin.client.index', [
            'clients' => $clients,
            'search' => $request->get('search'),
            ...$stats
        ]);
    }

    // =========================================================
    // Create Client
    // =========================================================
    public function create(): View
    {
        return view('admin.client.form', [
            'packages' => Package::select('id', 'name')->active()->get(),
            'user_id' => generate_client_user_id(),
        ]);
    }

    // =========================================================
    // Store Client
    // =========================================================
    public function store(ClientRequest $request): RedirectResponse
    {
        $this->clientService->create($request->validated(), $request);
        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    // =========================================================
    // Edit Client
    // =========================================================
    public function edit(Client $client): View
    {
        abort_if($client->parent_id, 403);

        return view('admin.client.form', [
            'client' => $client,
            'packages' => Package::active()->get(),
        ]);
    }

    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        abort_if($client->parent_id, 403);

        $this->clientService->update($client, $request->validated(), $request);

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    // =========================================================
    // Delete Client
    // =========================================================
    public function destroy(Client $client): RedirectResponse
    {
        abort_if($client->parent_id, 403);

        $this->clientService->delete($client);

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }

    // =========================================================
    // Show Client Details
    // =========================================================
    public function show(Client $client): View
    {
        return view('admin.client.show', compact('client'));
    }
}
