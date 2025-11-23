<?php

namespace App\Actions\Client\Auth;

use App\Domain\Clients\Models\Client;
use Illuminate\Support\Str;

class CreateClientAction
{
    public function execute(array $data): Client
    {
        return Client::create([
            'user_id'    => generate_client_user_id(),
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'password'   => $data['password'],
            'status'     => true,
            'role'       => 'super_admin',
        ]);
    }
}
