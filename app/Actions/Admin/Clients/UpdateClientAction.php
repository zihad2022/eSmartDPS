<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;

class UpdateClientAction
{
    public function execute(Client $client, array $data): Client
    {
        $updateData = [
            'parent_id' => $data['parent_id'] ?? $client->parent_id,
            'user_id' => $data['user_id'] ?? $client->user_id,
            'first_name' => $data['first_name'] ?? $client->first_name,
            'last_name' => $data['last_name'] ?? $client->last_name,
            'email' => $data['email'] ?? $client->email,
            'phone' => $data['phone'] ?? $client->phone,
            'division' => $data['division'] ?? $client->division,
            'district' => $data['district'] ?? $client->district,
            'address' => $data['address'] ?? $client->address,
            'postal_code' => $data['postal_code'] ?? $client->postal_code,
            'nid_number' => $data['nid_number'] ?? $client->nid_number,
            'nid_card_front' => $data['nid_card_front'] ?? $client->nid_card_front,
            'nid_card_back' => $data['nid_card_back'] ?? $client->nid_card_back,
            'profile_photo' => $data['profile_photo'] ?? $client->profile_photo,
            'status' => $data['status'] ?? $client->status,
        ];

        // Only update password if it's provided
        if (! empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $client->update($updateData);

        return $client;
    }
}
