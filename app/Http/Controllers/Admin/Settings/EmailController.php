<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    /**
     * Show the email settings edit form.
     *
     * Retrieves the current email settings from the database
     * and passes them to the view for editing.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Retrieve the first (and presumably only) admin settings record
        $settings = AdminSetting::first();

        // Return the 'email settings' view with the current settings
        return view('admin.settings.email', compact('settings'));
    }

    /**
     * Handle the email settings form submission and update settings.
     *
     * Validates the request data, updates the settings in the database,
     * then redirects back to the edit page with a success message.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate required email SMTP fields
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'required|string|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'email_message_template' => 'required|string',
        ]);

        // Retrieve the existing admin settings
        $settings = AdminSetting::first();

        // Update settings with validated data from the form
        $settings->update([
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
            'email_message_template' => $request->email_message_template,
        ]);

        // Log activity
        ActivityLogger::log('Email Settings Updated');

        // Redirect back to the edit page with a success flash message
        return redirect()
            ->route('admin.settings.email.edit')
            ->with('success', 'Email settings updated successfully.');
    }
}
