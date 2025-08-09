<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function edit()
    {
        $client = Client::findOrFail(owner_client_id());
        $settings = $client->settings;

        return view('client.settings.general', compact('settings'));
    }

    public function update(Request $request)
    {
        $client = Client::findOrFail(owner_client_id());

        $settings = $client->settings;

        $settings->update([
            'organization_name' => $request->organization_name,
            'short_name' => $request->short_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'currency' => $request->currency,
        ]);

        return redirect()
            ->route('client.settings.general.edit')
            ->with('success', 'Settings updated successfully.');
    }
}
