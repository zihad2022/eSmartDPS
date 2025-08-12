<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    /**
     * Show the SMS settings edit form.
     *
     * Retrieves the first (and presumably only) AdminSetting record
     * to populate the form with current SMS-related settings.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Retrieve existing settings to display in the form
        $settings = AdminSetting::first();

        // Return the SMS settings view with the current settings
        return view('admin.settings.sms', compact('settings'));
    }

    /**
     * Update the SMS settings in the database.
     *
     * Validates the incoming request, updates the settings,
     * and redirects back with a success message.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate only the fields related to SMS settings;
        // all fields are nullable strings to allow optional updates.
        $request->validate([
            'sms_api_key' => 'nullable|string',
            'sms_secret_key' => 'nullable|string',
            'sms_sender_id' => 'nullable|string',
            'sms_api_url' => 'nullable|string',
            'sms_balance_api' => 'nullable|string',
            'sms_message_template' => 'nullable|string',
        ]);

        // Fetch the first AdminSetting record to update
        $settings = AdminSetting::first();

        // Update the SMS-related fields with validated input data
        $settings->update([
            'sms_api_key' => $request->sms_api_key,
            'sms_secret_key' => $request->sms_secret_key,
            'sms_sender_id' => $request->sms_sender_id,
            'sms_api_url' => $request->sms_api_url,
            'sms_balance_api' => $request->sms_balance_api,
            'sms_message_template' => $request->sms_message_template,
        ]);

        // Log activity
        ActivityLogger::log('SMS Settings Updated');

        // Redirect back to the SMS settings edit page with a success message
        return redirect()
            ->route('admin.settings.sms.edit')
            ->with('success', 'SMS settings updated successfully.');
    }
}
