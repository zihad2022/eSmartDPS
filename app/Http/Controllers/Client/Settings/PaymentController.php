<?php

namespace App\Http\Controllers\Client\Settings;

use App\Http\Controllers\Controller;
use App\Domain\Clients\Models\Client;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function edit()
    {
        // Get the currently authenticated client's record
        $client = Client::findOrFail(owner_client_id());

        // Check if the client has settings 
        if (! $client->settings) {
            return redirect()->route('client.settings.payment.create');
        }

        // Load the related settings
        $settings = $client->settings;

        // Return the settings Blade view with the settings data
        return view('client.settings.payment', compact('settings'));
    }

    public function update(Request $request)
{
    // Get the authenticated client with settings
    $client = Client::with('settings')->findOrFail(owner_client_id());

    if (! $client->settings) {
        return redirect()
            ->route('client.settings.payment.create')
            ->with('error', 'Please create your settings first.');
    }

    // Validate incoming request
    $validated = $request->validate([
        'payment_due_date'    => 'nullable|integer|min:1|max:30',   // e.g., "1", "15", "30"
        'late_payment_fee'    => 'nullable|numeric|min:0',
        'grace_period_days'   => 'nullable|integer|min:0',
        'payment_methods'     => 'nullable|array',           // must be array for JSON storage
        'payment_methods.*'   => 'integer|in:1,2,3,4,5',     // valid enum values
    ]);

    // Update settings
    $client->settings->update([
        'payment_due_date'   => $validated['payment_due_date'] ?? null,
        'late_payment_fee'   => $validated['late_payment_fee'] ?? null,
        'grace_period_days'  => $validated['grace_period_days'] ?? null,
        'payment_methods'    => $validated['payment_methods'] ?? [], // store as JSON
    ]);

    return redirect()
        ->route('client.settings.payment.edit')
        ->with('success', 'Payment settings updated successfully.');
}

}
