<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\DTOs\ClientData;
use App\Domain\Clients\Models\Client;
use App\Http\Requests\Admin\ClientRequest;

class PrepareClientDataAction
{
    public function execute(ClientRequest $request, ?Client $client = null): ClientData
    {
        $v = $request->validated();

        return new ClientData(
            user_id: $client->user_id ?? generate_client_user_id(),
            first_name: $v['first_name'],
            last_name: $v['last_name'],
            email: $v['email'],
            phone: $v['phone'] ?? null,
            division: $v['division'] ?? null,
            district: $v['district'] ?? null,
            address: $v['address'] ?? null,
            postal_code: $v['postal_code'] ?? null,
            nid_number: $v['nid_number'] ?? null,
            nid_card_front: $request->file('nid_card_front'),
            nid_card_back: $request->file('nid_card_back'),
            profile_photo: $request->file('profile_photo'),
            password: $v['password'] ?? null,
            package_id: $v['package_id'] ?? null,
        );
    }
}
