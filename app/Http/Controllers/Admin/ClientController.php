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
    public function __construct(
        private readonly ImageService $imageService,
        private readonly PackageService $packageService,
        private readonly MailService $mailService
    ) {}

    /**
     * Display a paginated list of main clients.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search'); // Search clients by multiple fields
        $status = $request->get('status'); // Filter clients by status

        $clients = Client::parents()
            ->filterBySearch($search) // Search clients by multiple fields
            ->filterByStatus($status) // Filter clients by status
            ->latest() // Order clients by latest
            ->paginate(10) // Paginate clients
            ->appends($request->query()); // Append query parameters

        $stats = $this->collectClientStats(); // Collect client statistics

        return view('admin.client.index', array_merge($stats, [
            'clients' => $clients,
            'search' => $search,
        ]));
    }

    /**
     * Show form to create a new client.
     */
    public function create(): View
    {
        $packages = Package::active()->get();
        $user_id = generate_client_user_id();

        return view('admin.client.form', compact('packages', 'user_id'));
    }

    /**
     * Store a newly created client.
     */
    public function store(ClientRequest $request): RedirectResponse
    {
        $validated = $this->prepareClientData($request->validated(), $request);

        $client = Client::create($validated);

        $this->mailService->sendMail($client, $request->password);

        $this->assignPackageIfProvided($client, $request);

        return $this->redirectWithSuccess('Client created successfully.');
    }

    /**
     * Show client details.
     */
    public function show(Client $client): View
    {
        return view('admin.client.show', compact('client'));
    }

    /**
     * Show form to edit an existing client.
     */
    public function edit(Client $client): View
    {
        $this->abortIfSubClient($client);

        $packages = Package::active()->get();

        return view('admin.client.form', compact('client', 'packages'));
    }

    /**
     * Update a client.
     */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        $this->abortIfSubClient($client);

        $validated = $this->prepareClientData($request->validated(), $request, $client);

        $client->update($validated);

        $this->assignPackageIfProvided($client, $request);

        return $this->redirectWithSuccess('Client updated successfully.');
    }

    /**
     * Delete a client and its images.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $this->abortIfSubClient($client);

        $this->deleteClientImages($client);

        $client->delete();

        return $this->redirectWithSuccess('Client deleted successfully.');
    }

    /* ----------------------- Helper Methods ----------------------- */

    private function prepareClientData(array $data, Request $request, ?Client $client = null): array
    {
        $data = $this->handleImages($data, $request, $client);
        $data['parent_id'] = null;
        $data['role'] = 'super_admin';
        $data['password'] = $this->hashPassword($data['password'] ?? null, $client);

        unset($data['package_id']);

        return $data;
    }

    private function handleImages(array $data, Request $request, ?Client $client = null): array
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            if ($request->hasFile($field)) {
                $client?->$field && $this->imageService->deleteImage($client->$field);

                $data[$field] = $this->imageService->uploadImage(
                    $request->file($field),
                    'uploads/clients'
                );
            }
        }

        return $data;
    }

    private function hashPassword(?string $password, ?Client $client): ?string
    {
        if (!$password) return $client->password ?? null;

        return Hash::make($password);
    }

    private function deleteClientImages(Client $client): void
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            $this->imageService->deleteImage($client->$field);
        }
    }

    private function assignPackageIfProvided(Client $client, Request $request): void
    {
        if ($packageId = $request->package_id) {
            $package = Package::find($packageId);
            $package && $this->packageService->startPackage($client, $package);
        }
    }

    private function abortIfSubClient(Client $client): void
    {
        abort_if($client->parent_id, 403);
    }

    private function redirectWithSuccess(string $message): RedirectResponse
    {
        return redirect()->route('admin.clients.index')->with('success', $message);
    }

    private function collectClientStats(): array
    {
        return [
            'totalClients' => Client::parents()->count(),
            'activeClients' => Client::activeParents()->count(),
            'inactiveClients' => Client::inactiveParents()->count(),
        ];
    }
}
