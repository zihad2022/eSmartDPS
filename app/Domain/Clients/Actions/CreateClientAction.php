<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\DTOs\ClientData;
use App\Domain\Clients\Models\Client;
use Illuminate\Support\Facades\Hash;

class CreateClientAction
{
    public function execute(ClientData $data): Client
    {
        return Client::create([
            'parent_id' => $data->parent_id,
            'user_id' => $data->user_id,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'phone' => $data->phone,
            'division' => $data->division,
            'district' => $data->district,
            'address' => $data->address,
            'postal_code' => $data->postal_code,
            'nid_number' => $data->nid_number,
            'nid_card_front' => $data->nid_card_front,
            'nid_card_back' => $data->nid_card_back,
            'profile_photo' => $data->profile_photo,
            'password' => $data->password,
            'role'=> 'super-admin',
        ]);
    }
}
