<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Domain\Clients\Models\Client;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function edit()
    {
        // Get the currently authenticated client's record
        $client = Client::findOrFail(owner_client_id());

        // Check if the client has settings 
        if (! $client->settings) {
            return redirect()->route('client.settings.notification.create');
        }

        // Load the related settings
        $settings = $client->settings;

        // Return the settings Blade view with the settings data
        return view('client.settings.notification', compact('settings'));
    }

    public function update(Request $request)
    {
        $client = Client::findOrFail(owner_client_id());
    
        $validated = $request->validate([
            'sms_api_provider' => 'nullable|string|max:255',
            'sms_api_key'      => 'nullable|string|max:255',
            'email_payment_confirmations'   => 'boolean',
            'email_payment_reminders'  => 'boolean',
            'email_payment_reports'    => 'boolean',
            'sms_payment_confirmations'     => 'boolean',
            'sms_payment_reminders'    => 'boolean',
        ]);
    
        $client->settings()->update($validated);
    
        return redirect()->route('client.settings.notification.edit')
            ->with('success', 'Notification settings updated successfully!');
    }
    
}
