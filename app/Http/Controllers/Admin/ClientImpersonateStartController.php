<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Clients\StartClientImpersonationAction;
use App\Domain\Clients\Models\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class ClientImpersonateStartController extends Controller
{
    public function __invoke(Client $client, StartClientImpersonationAction $action): RedirectResponse
    {
        $action->execute($client);

        return redirect()->route('client.dashboard')
            ->with('success', 'You are now logged in as client: '.$client->full_name);
    }
}
