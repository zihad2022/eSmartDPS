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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
        $search = trim((string) $request->get('search', '')) ?: null;
        $status = trim((string) $request->get('status', '')) ?: null;

        $clients = $this->clientRepo->searchAndFilter($search, $status);

        $stats = $this->clientRepo->getStats();

        return view('admin.client.index', [
            'clients' => $clients,
            'search' => $search,
            ...$stats
        ]);
    }

    // =========================================================
    // Create Client
    // =========================================================
    public function create(): View
    {
        $packages = Cache::remember('packages.active.simple', now()->addMinutes(5), function () {
            return Package::select('id', 'name')->active()->get();
        });

        return view('admin.client.form', [
            'packages' => $packages,
            'user_id' => generate_client_user_id(),
        ]);
    }

    // =========================================================
    // Store Client
    // =========================================================
    public function store(ClientRequest $request): RedirectResponse
    {
        try {
            $this->clientService->create($request->validated(), $request);
            return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to create client', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Failed to create client. Please try again.');
        }
    }

    // =========================================================
    // Edit Client
    // =========================================================
    public function edit(Client $client): View
    {
        $this->guardRootClient($client);
        $packages = Cache::remember('packages.active.simple', now()->addMinutes(5), function () {
            return Package::select('id', 'name')->active()->get();
        });

        return view('admin.client.form', [
            'client' => $client,
            'packages' => $packages,
        ]);
    }

    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        $this->guardRootClient($client);

        try {
            $this->clientService->update($client, $request->validated(), $request);
            return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to update client', ['client_id' => $client->id, 'error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Failed to update client. Please try again.');
        }
    }

    // =========================================================
    // Delete Client
    // =========================================================
    public function destroy(Client $client): RedirectResponse
    {
        $this->guardRootClient($client);

        try {
            $this->clientService->delete($client);
            return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to delete client', ['client_id' => $client->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete client. Please try again.');
        }
    }

    // =========================================================
    // Show Client Details
    // =========================================================
    public function show(Client $client): View
    {
        return view('admin.client.show', compact('client'));
    }

    // =========================================================
    // Helpers
    // =========================================================
    private function guardRootClient(Client $client): void
    {
        abort_if($client->parent_id, 403);
    }
}
