<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\Client;
use App\Models\Package;
use App\Services\ImageService;
use App\Services\MailService;
use App\Services\PackageService;
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
        private readonly PackageService $packageService,
        private readonly MailService $mailService
    ) {}

    /**
     * Display a paginated list of all main clients (excluding sub-clients).
     */
    public function index(Request $request): View
    {
        // -----------------------------
        // 1. Handle search query
        // -----------------------------
        // Search clients by multiple fields (first name, last name, email, etc.)
        $search = $request->get('search');

        // -----------------------------
        // 2. Build query for main clients
        // -----------------------------
        $clients = Client::query()
            ->whereNull('parent_id') // Only top-level clients
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('user_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('nid_number', 'like', "%{$search}%")
                        ->orWhere('division', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('postal_code', 'like', "%{$search}%");
                });
            })
            ->when($request->get('status'), fn ($q, $status) => $q->where('status', $status === 'active'))
            ->latest('id')
            ->paginate(10)
            ->appends($request->query());

        // -----------------------------
        // 3. Collect statistics
        // -----------------------------
        $totalClients = Client::parents()->count();
        $activeClients = Client::activeParents()->count();
        $inactiveClients = Client::inactiveParents()->count();

        // -----------------------------
        // 4. Return view
        // -----------------------------
        return view('admin.client.index', compact(
            'clients',
            'totalClients',
            'activeClients',
            'inactiveClients',
            'search'
        ));
    }

    /**
     * Show the form to create a new client.
     */
    public function create(): View
    {
        // -----------------------------
        // 1. Fetch active packages
        // -----------------------------
        $packages = Package::active()->get();

        // -----------------------------
        // 2. Generate unique user ID
        // -----------------------------
        $user_id = generate_client_user_id();

        // -----------------------------
        // 3. Return view
        // -----------------------------
        return view('admin.client.form', compact('packages', 'user_id'));
    }

    /**
     * Store a new client in the database.
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        // -----------------------------
        // 1. Prepare validated client data
        // -----------------------------
        $validated = $this->prepareClientData($request->validated(), $request);

        // -----------------------------
        // 2. Create client
        // -----------------------------
        $client = Client::create($validated);

        // -----------------------------
        // 3. Send credentials via email
        // -----------------------------
        $this->mailService->sendMail($client, $request['password']);

        // -----------------------------
        // 4. Assign selected package
        // -----------------------------
        $package = Package::find($request['package_id']);
        $this->packageService->startPackage($client, $package);

        // -----------------------------
        // 5. Redirect with success
        // -----------------------------
        return redirect()->route('admin.clients.index')
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
        // -----------------------------
        // 1. Prevent editing sub-clients
        // -----------------------------
        abort_if($client->parent_id, 403);

        // -----------------------------
        // 2. Fetch active packages
        // -----------------------------
        $packages = Package::active()->get();

        // -----------------------------
        // 3. Return view
        // -----------------------------
        return view('admin.client.form', compact('client', 'packages'));
    }

    /**
     * Update an existing client.
     */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        // -----------------------------
        // 1. Prevent editing sub-clients
        // -----------------------------
        abort_if($client->parent_id, 403);

        // -----------------------------
        // 2. Prepare validated data
        // -----------------------------
        $validated = $this->prepareClientData($request->validated(), $request, $client);

        // -----------------------------
        // 3. Update client
        // -----------------------------
        $client->update($validated);

        // -----------------------------
        // 4. Assign package if selected
        // -----------------------------
        if ($request['package_id']) {
            $this->packageService->startPackage($client, Package::find($request['package_id']));
        }

        // -----------------------------
        // 5. Redirect with success
        // -----------------------------
        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Delete a client and remove related images.
     */
    public function destroy(Client $client): RedirectResponse
    {
        // -----------------------------
        // 1. Prevent deleting sub-clients
        // -----------------------------
        abort_if($client->parent_id, 403);

        // -----------------------------
        // 2. Delete client images
        // -----------------------------
        $this->deleteClientImages($client);

        // -----------------------------
        // 3. Delete client record
        // -----------------------------
        $client->delete();

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Helper: Prepare validated client data for create/update.
     */
    private function prepareClientData(array $data, Request $request, Client $client = null): array
    {
        // -----------------------------
        // 1. Handle image uploads
        // -----------------------------
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if exists
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

        // -----------------------------
        // 2. Ensure root-level client
        // -----------------------------
        $data['parent_id'] = null;
        $data['role'] = 'super_admin';

        // -----------------------------
        // 3. Handle password hashing
        // -----------------------------
        $data['password'] = !empty($data['password'])
            ? Hash::make($data['password'])
            : ($client->password ?? null);

        // -----------------------------
        // 4. Remove package_id (handled separately)
        // -----------------------------
        unset($data['package_id']);

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
