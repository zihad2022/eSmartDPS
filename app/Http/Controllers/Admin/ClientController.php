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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected PakageService $pakageService
    ) {
    }

    public function index(Request $request)
    {
        $clients = Client::query()
            ->whereNull('parent_id')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status === 'active')
            )
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.client.index', [
            'clients' => $clients,
            'totalMembers' => Member::count(),
            'activeMembers' => Member::active()->count(),
            'inactiveMembers' => Member::inactive()->count(),
        ]);
    }

    public function create()
    {
        return view('admin.client.form', [
            'pakages' => Pakage::active()->get(),
            'user_id' => generate_client_user_id(),
        ]);
    }

    public function store(ClientRequest $request)
    {
        $validated = $request->validated();
        $pakageId = $validated['pakage_id'];

        $validated = $this->processClientData($request, $validated);
        $validated['role'] = 'admin';

        $mailService = new MailService();
        $client = Client::create($validated);

        $mailService->sendMail($client);

        $this->pakageService->startPakage($client, Pakage::find($pakageId));

        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('admin.client.show', compact('client'));
    }

    public function edit(Client $client)
    {
        abort_if($client->parent_id, 403);

        return view('admin.client.form', [
            'client' => $client,
            'pakages' => Pakage::active()->get(),
        ]);
    }

    public function update(ClientRequest $request, Client $client)
    {
        abort_if($client->parent_id, 403);

        $validated = $request->validated();
        $pakageId = $validated['pakage_id'] ?? null;

        $validated = $this->processClientData($request, $validated, $client);

        $client->update($validated);

        if ($pakageId) {
            $this->pakageService->startPakage($client, Pakage::find($pakageId));
        }

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        abort_if($client->parent_id, 403);

        $this->deleteClientImages($client);
        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }

    /** 🔹 Handles image uploads and password hashing */
    private function processClientData(Request $request, array $data, Client $client = null): array
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            if ($request->hasFile($field)) {
                if ($client?->$field) {
                    $this->imageService->deleteImage($client->$field);
                }
                $data[$field] = $this->imageService->uploadImage($request->file($field), 'uploads/clients');
            }
        }

        $data['parent_id'] = null;
        // Handle password
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['pakage_id']);

        return $data;
    }

    /** 🔹 Deletes all images of a client */
    private function deleteClientImages(Client $client): void
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            $this->imageService->deleteImage($client->$field);
        }
    }
}
