<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClientRequest;
use App\Models\Client;
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
    public function __construct(
        private readonly ImageService $imageService,
        private readonly PakageService $pakageService,
        private readonly MailService $mailService
    ) {
    }

    /** Display all clients */
    public function index(Request $request): View
    {
        $clients = Client::query()
            ->whereNull('parent_id')
            ->when($request->get('status'), fn ($q, $status) => $q->where('status', $status === 'active'))
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

    /** Show create form */
    public function create(): View
    {
        return view('admin.client.form', [
            'pakages' => Pakage::active()->get(),
            'user_id' => generate_client_user_id(),
        ]);
    }

    /** Store a new client */
    public function store(ClientRequest $request): RedirectResponse
    {
        $validated = $this->prepareClientData($request->validated(), $request);

        $client = Client::create($validated);

        $this->mailService->sendMail($client, $request->password);
        $this->pakageService->startPakage($client, Pakage::find($request->pakage_id));

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    /** Show client details */
    public function show(Client $client): View
    {
        return view('admin.client.show', compact('client'));
    }

    /** Show edit form */
    public function edit(Client $client): View
    {
        abort_if($client->parent_id, 403);

        return view('admin.client.form', [
            'client' => $client,
            'pakages' => Pakage::active()->get(),
        ]);
    }

    /** Update client */
    public function update(ClientRequest $request, Client $client): RedirectResponse
    {
        abort_if($client->parent_id, 403);

        $validated = $this->prepareClientData($request->validated(), $request, $client);
        $client->update($validated);

        if ($request->pakage_id) {
            $this->pakageService->startPakage($client, Pakage::find($request->pakage_id));
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /** Delete client */
    public function destroy(Client $client): RedirectResponse
    {
        abort_if($client->parent_id, 403);

        $this->deleteClientImages($client);
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /** 🔹 Process input data for create/update */
    private function prepareClientData(array $data, Request $request, Client $client = null): array
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            if ($request->hasFile($field)) {
                if ($client?->$field) {
                    $this->imageService->deleteImage($client->$field);
                }
                $data[$field] = $this->imageService->uploadImage(
                    $request->file($field),
                    'uploads/clients'
                );
            }
        }

        $data['parent_id'] = null;
        $data['password'] = ! empty($data['password']) ? Hash::make($data['password']) : ($client->password ?? null);
        unset($data['pakage_id']);

        return $data;
    }

    /** 🔹 Delete client images */
    private function deleteClientImages(Client $client): void
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            $this->imageService->deleteImage($client->$field);
        }
    }
}
