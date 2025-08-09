<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    /**
     * Show the contact information settings edit form.
     *
     * Fetches the first AdminSetting record (assuming a single row)
     * and passes it to the view for editing contact info settings.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Retrieve the existing settings record from the database.
        $settings = AdminSetting::first();

        // Return the edit view with the current settings data.
        return view('admin.settings.contact-info', compact('settings'));
    }

    /**
     * Handle the update request for contact information settings.
     *
     * Validates the incoming request data, updates the settings record,
     * and redirects back with a success message.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate the incoming request data.
        // All fields are required for contact information settings.
        $request->validate([
            'helpline_number' => 'required|string|max:255',
            'email_address' => 'required|email|max:255',
            'office_address' => 'required|string|max:1000',
            'google_map' => 'required|string',
        ]);

        // Fetch the existing settings record to update.
        $settings = AdminSetting::first();

        // Update the contact information fields with validated data.
        $settings->update([
            'helpline_number' => $request->helpline_number,
            'email_address' => $request->email_address,
            'office_address' => $request->office_address,
            'google_map' => $request->google_map,
        ]);

        // Redirect back to the edit page with a success flash message.
        return redirect()
            ->route('admin.settings.contact_info.edit')
            ->with('success', 'Contact info updated successfully.');
    }
}
