<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function edit()
    {
        // Get the currently authenticated client's record
        $client = Client::findOrFail(owner_client_id());

        // Check if the client has settings
        if (! $client->settings) {
            return redirect()->route('client.settings.share.create');
        }

        // Load the related settings
        $settings = $client->settings;

        // Return the settings Blade view with the settings data
        return view('client.settings.share', compact('settings'));
    }

    public function update(Request $request)
    {
        // Get the authenticated client
        $client = Client::findOrFail(owner_client_id());

        // Check if the client has settings
        if (! $client->settings) {
            return redirect()->route('client.settings.share.create');
        }

        // Load client's settings
        $settings = $client->settings;

        // Validate incoming request (optional but recommended)
        $validated = $request->validate([
            'share_price' => 'nullable|numeric',
            'minimum_shares' => 'nullable|numeric',
            'maximum_shares' => 'nullable|numeric',
            'share_transfer_fee' => 'nullable|numeric',
            'allow_partial_shares' => 'nullable|boolean',
        ]);

        // Update settings with validated data
        $settings->update($validated);

        // Redirect back to the edit page with success message
        return redirect()
            ->route('client.settings.share.edit')
            ->with('success', 'Share settings updated successfully.');
    }
}
