<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    /**
     * Show the form for editing social media settings.
     *
     * Retrieves the first AdminSetting record which holds the social media URLs.
     * Passes the settings data to the 'admin.settings.social-media' Blade view.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Fetch the first record from admin_settings table
        $settings = AdminSetting::first();

        // Return the social media settings edit view with existing data
        return view('admin.settings.social-media', compact('settings'));
    }

    /**
     * Update the social media settings in the database.
     *
     * Validates the request input to ensure all social media fields are present.
     * Updates the AdminSetting record with new values.
     * Redirects back to the edit form with a success message on completion.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate that all social media URLs are provided and not empty
        $request->validate([
            'facebook_page' => 'required|string',
            'facebook_group' => 'required|string',
            'whatsapp_channel' => 'required|string',
            'telegram_channel' => 'required|string',
            'linkedin' => 'required|string',
            'twitter_x' => 'required|string',
            'youtube' => 'required|string',
            'tiktok' => 'required|string',
        ]);

        // Retrieve the existing settings record
        $settings = AdminSetting::first();

        // Update the settings with new social media URLs
        $settings->update([
            'facebook_page' => $request->facebook_page,
            'facebook_group' => $request->facebook_group,
            'whatsapp_channel' => $request->whatsapp_channel,
            'telegram_channel' => $request->telegram_channel,
            'linkedin' => $request->linkedin,
            'twitter_x' => $request->twitter_x,
            'youtube' => $request->youtube,
            'tiktok' => $request->tiktok,
        ]);

        // Log activity
        ActivityLogger::log('Social Media Settings Updated');

        // Redirect back to the edit form with a success flash message
        return redirect()
            ->route('admin.settings.social_media.edit')
            ->with('success', 'Social media settings updated successfully.');
    }
}
