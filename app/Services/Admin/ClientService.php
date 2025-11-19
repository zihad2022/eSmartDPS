<?php

namespace App\Services\Admin;

use App\Events\Admin\ClientCreated;
use App\Domain\Clients\Models\Client;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientService
{
    public function __construct(private readonly ImageService $imageService) {}

    public function create(array $data, Request $request): Client
    {
        $data = $this->prepareData($data, $request);
        $client = Client::create($data);

        // Fire Event
        event(new ClientCreated($client, $request->package_id, $request->password));

        return $client;
    }

    public function update(Client $client, array $data, Request $request): Client
    {
        $data = $this->prepareData($data, $request, $client);
        $client->update($data);

        return $client;
    }

    public function delete(Client $client): void
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            $this->imageService->deleteImage($client->$field);
        }
        $client->delete();
    }

    private function prepareData(array $data, Request $request, ?Client $client = null): array
    {
        $data['role'] = 'super_admin';
        $data['parent_id'] = null;

        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            if ($request->hasFile($field)) {
                $client?->$field && $this->imageService->deleteImage($client->$field);
                $data[$field] = $this->imageService->uploadImage($request->file($field), 'uploads/clients');
            }
        }

        $data['password'] = $data['password'] ?? null
            ? Hash::make($data['password'])
            : ($client->password ?? null);

        unset($data['package_id']);

        return $data;
    }
}
