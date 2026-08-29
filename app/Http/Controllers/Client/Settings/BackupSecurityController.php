<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class BackupSecurityController extends Controller
{
    public function edit()
    {
        // Get the currently authenticated client's record
        $client = Client::findOrFail(owner_client_id());

        // Check if the client has settings
        if (! $client->settings) {
            return redirect()->route('client.settings.backup-security.edit');
        }

        // Load the related settings
        $settings = $client->settings;

        // Return the settings Blade view with the settings data
        return view('client.settings.backup-security', compact('settings'));
    }

    public function update(Request $request)
    {
        $client = Client::findOrFail(owner_client_id());

        $validated = $request->validate([
            'auto_backup' => 'boolean',
            'two_factor_auth' => 'boolean',
            'session_timeout' => 'boolean',
            'login_notifications' => 'boolean',
        ]);

        $client->settings()->update($validated);

        return redirect()->route('client.settings.backup-security.edit')
            ->with('success', 'Backup & Security settings updated successfully!');
    }
}
