<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\Client;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Pakage;
use App\Services\ImageService;
use App\Services\MailService;
use App\Services\PakageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Inject necessary services for image handling, package management, and email.
     * Using readonly ensures these cannot be modified later.
     */
    public function __construct(
        private readonly ImageService $imageService,
        private readonly PakageService $pakageService,
        private readonly MailService $mailService
    ) {
    }

    /**
     * Display a paginated list of all clients (excluding sub-clients).
     */
    public function index(Request $request): View
    {
        $clients = Client::query()
            ->whereNull('parent_id')
            ->when(
                $request->get('status'),
                fn ($q, $status) => $q->where('status', $status === 'active')
            )
            ->latest('id')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.client.index', [
            'clients' => $clients,
            'totalMembers' => Member::count(),
            'activeMembers' => Member::active()->count(),
            'inactiveMembers' => Member::inactive()->count(),
        ]);
    }

    /**
     * Show the form to create a new client.
     */
    public function create(): View
    {
        return view('admin.client.form', [
            'pakages' => Pakage::active()->get(),
            'user_id' => generate_client_user_id(),
        ]);
    }

    /**
     * Store a new client in the database.
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        // Prepare and validate client data
        $validated = $this->prepareClientData($request->validated(), $request);

        // Create the client
        $client = Client::create($validated);

        // Send credentials to the client via email
        $this->mailService->sendMail($client, $request['password']);

        // Assign the selected package to the client
        $pakage = Pakage::find($request['pakage_id']);
        $this->pakageService->startPakage($client, $pakage);

        // Create client settings
        ClientSetting::create([
            'client_id' => $client->id,
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Show a single client details page.
     */
    public function show(Client $client): View
    {
        return view('admin.client.show', compact('client'));
    }

    /**
     * Show the edit form for an existing client.
     */
    public function edit(Client $client): View
    {
        abort_if($client->parent_id, 403); // Prevent editing sub-clients

        return view('admin.client.form', [
            'client' => $client,
            'pakages' => Pakage::active()->get(),
        ]);
    }

    /**
     * Update an existing client.
     */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        abort_if($client->parent_id, 403);

        // Prepare validated data (including image updates)
        $validated = $this->prepareClientData($request->validated(), $request, $client);
        $client->update($validated);

        // If a package is selected, start it
        if ($request['pakage_id']) {
            $this->pakageService->startPakage($client, Pakage::find($request['pakage_id']));
        }

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Delete a client and remove related images.
     */
    public function destroy(Client $client): RedirectResponse
    {
        abort_if($client->parent_id, 403);

        $this->deleteClientImages($client); // Remove uploaded files
        $client->delete(); // Remove record

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Helper: Prepare validated client data for create/update.
     * - Handles image uploads
     * - Hashes password if provided
     * - Ensures parent_id is always null for main clients
     */
    private function prepareClientData(array $data, Request $request, Client $client = null): array
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if updating
                if ($client?->$field) {
                    $this->imageService->deleteImage($client->$field);
                }

                // Upload new image
                $data[$field] = $this->imageService->uploadImage(
                    $request->file($field),
                    'uploads/clients'
                );
            }
        }

        // Ensure root-level clients have no parent
        $data['parent_id'] = null;
        $data['role'] = 'admin';
        // If password is not provided, keep the old one
        $data['password'] = ! empty($data['password'])
            ? Hash::make($data['password'])
            : ($client->password ?? null);

        // Remove pakage_id from client table (handled separately)
        unset($data['pakage_id']);

        return $data;
    }

    /**
     * Helper: Delete all uploaded images for a client.
     */
    private function deleteClientImages(Client $client): void
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            $this->imageService->deleteImage($client->$field);
        }
    }
}
