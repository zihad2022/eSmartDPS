<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Services\ImageService;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Inject the ImageService dependency via constructor.
     *
     * @param ImageService $imageService Handles image upload and deletion logic
     */
    public function __construct(protected ImageService $imageService)
    {
    }

    /**
     * Show the general settings edit form.
     *
     * Fetches the first (and only) AdminSetting record and passes it to the view.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Retrieve the general settings from the database
        $settings = AdminSetting::first();

        // Return the general settings view with the settings data
        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update the general settings.
     *
     * Validates the incoming request, handles image uploads, deletes old images,
     * and updates the settings record.
     *
     * @param Request $request Incoming HTTP request containing form data and files
     * @return \Illuminate\Http\RedirectResponse Redirect back with success message
     */
    public function update(Request $request)
    {
        // Validate incoming form data with rules for text fields and images
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_slogan' => 'required|string|max:255',
            'site_description' => 'required|string',
            'site_keywords' => 'required|string',
            'meta_codes' => 'required|string',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:png|max:2048',
            'graph_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Retrieve current settings record
        $settings = AdminSetting::first();

        // Initialize array to store uploaded image paths (if any)
        $images = [];

        // Handle site_logo image upload
        if ($request->hasFile('site_logo')) {
            // Delete old site logo from storage
            $this->imageService->deleteImage($settings->site_logo);

            // Upload new site logo and store path
            $images['site_logo'] = $this->imageService->updateImage($request->file('site_logo'), 'settings');
        }

        // Handle favicon image upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon from storage
            $this->imageService->deleteImage($settings->favicon);

            // Upload new favicon and store path
            $images['favicon'] = $this->imageService->updateImage($request->file('favicon'), 'settings');
        }

        // Handle graph_thumbnail image upload
        if ($request->hasFile('graph_thumbnail')) {
            // Delete old graph thumbnail from storage
            $this->imageService->deleteImage($settings->graph_thumbnail);

            // Upload new graph thumbnail and store path
            $images['graph_thumbnail'] = $this->imageService->updateImage($request->file('graph_thumbnail'), 'settings');
        }

        // Update settings with validated data and newly uploaded images (if any)
        $settings->update([
            'site_name' => $request->site_name,
            'site_slogan' => $request->site_slogan,
            'site_description' => $request->site_description,
            'site_keywords' => $request->site_keywords,
            'meta_codes' => $request->meta_codes,
            // Use new image path if uploaded, otherwise keep existing
            'site_logo' => $images['site_logo'] ?? $settings->site_logo,
            'favicon' => $images['favicon'] ?? $settings->favicon,
            'graph_thumbnail' => $images['graph_thumbnail'] ?? $settings->graph_thumbnail,
        ]);

        // Redirect back to the edit page with a success flash message
        return redirect()
            ->route('admin.settings.general.edit')
            ->with('success', 'General settings updated successfully.');
    }
}
