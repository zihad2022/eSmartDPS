<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Domain\Clients\Models\Client;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Show the General Settings form for the authenticated client.
     */
    public function edit()
    {
        // Get the currently authenticated client's record
        $client = Client::findOrFail(owner_client_id());

        // Check if the client has settings 
        if (! $client->settings) {
            return redirect()->route('client.settings.general.create');
        }

        // Load the related settings
        $settings = $client->settings;

        // Return the settings Blade view with the settings data
        return view('client.settings.general', compact('settings'));
    }

    /**
     * Update the general settings for the authenticated client.
     */
    public function update(Request $request)
    {
        // Get the authenticated client
        $client = Client::findOrFail(owner_client_id());

        // Check if the client has settings
        if (! $client->settings) {
            return redirect()->route('client.settings.general.create');
        }

        // Load client's settings
        $settings = $client->settings;

        // Validate incoming request (optional but recommended)
        $validated = $request->validate([
            'organization_name' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'currency' => 'nullable|string|max:10',
        ]);

        // Update settings with validated data
        $settings->update($validated);

        // Redirect back to the edit page with success message
        return redirect()
            ->route('client.settings.general.edit')
            ->with('success', 'General settings updated successfully.');
    }
}