<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;

class CreateClientAction
{
    public function execute(array $data): Client
    {
        return Client::create([
            'parent_id' => $data['parent_id'] ?? null,
            'user_id' => $data['user_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'division' => $data['division'] ?? null,
            'district' => $data['district'] ?? null,
            'address' => $data['address'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'nid_number' => $data['nid_number'] ?? null,
            'nid_card_front' => $data['nid_card_front'] ?? null,
            'nid_card_back' => $data['nid_card_back'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null,
            'password' => $data['password'] ?? null,
            'role' => $data['role'] ?? 'super-admin',
            'status' => $data['status'] ?? true,
        ]);
    }
}
