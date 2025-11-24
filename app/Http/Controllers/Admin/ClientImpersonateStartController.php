<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Clients\Models\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ClientImpersonateStartController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Client $client)
    {
        // Store admin ID so you can return back later
        Session::put('impersonate_admin_id', Auth::guard('admin')->id());

        // Login as client
        Auth::guard('client')->login($client);

        return redirect()->route('client.dashboard')
            ->with('success', 'You are now logged in as client: ' . $client->first_name);
    }
}
