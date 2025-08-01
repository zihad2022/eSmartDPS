<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Services\ImageService;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    public function edit()
    {
        $settings = AdminSetting::first();

        return view('admin.settings.general', compact('settings'));
    }

    public function update(Request $request)
    {
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
        $settings = AdminSetting::first();
        $images = [];
        if ($request->hasFile('site_logo')) {
            $this->imageService->deleteImage($settings->site_logo);
            $images['site_logo'] = $this->imageService->updateImage($request->file('site_logo'), 'settings');
        }
        if ($request->hasFile('favicon')) {
            $this->imageService->deleteImage($settings->favicon);
            $images['favicon'] = $this->imageService->updateImage($request->file('favicon'), 'settings');
        }
        if ($request->hasFile('graph_thumbnail')) {
            $this->imageService->deleteImage($settings->graph_thumbnail);
            $images['graph_thumbnail'] = $this->imageService->updateImage($request->file('graph_thumbnail'), 'settings');
        }
        $settings->update([
            'site_name' => $request->site_name,
            'site_slogan' => $request->site_slogan,
            'site_description' => $request->site_description,
            'site_keywords' => $request->site_keywords,
            'meta_codes' => $request->meta_codes,
            'site_logo' => $images['site_logo'] ?? $settings->site_logo,
            'favicon' => $images['favicon'] ?? $settings->favicon,
            'graph_thumbnail' => $images['graph_thumbnail'] ?? $settings->graph_thumbnail,
        ]);

        return redirect()->route('admin.settings.general.edit')->with('success', 'General settings updated successfully.');
    }
}
