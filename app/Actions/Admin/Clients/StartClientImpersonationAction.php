<?php

namespace App\Actions\Admin\Clients;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StartClientImpersonationAction
{
    public function execute(Client $client): void
    {
        abort_if($client->parent_id !== null, 403, 'Only organization owner accounts can be impersonated.');
        abort_unless($client->status, 422, 'The selected client account is inactive.');

        Session::put('impersonate_admin_id', Auth::guard('admin')->id());
        Auth::guard('client')->login($client);
        request()->session()->regenerate();
    }
}
