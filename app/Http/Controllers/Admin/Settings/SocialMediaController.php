<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function edit()
    {
        $settings = AdminSetting::first();

        return view('admin.settings.social-media', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'facebook_page' => 'required',
            'facebook_group' => 'required',
            'whatsapp_channel' => 'required',
            'telegram_channel' => 'required',
            'linkedin' => 'required',
            'twitter_x' => 'required',
            'youtube' => 'required',
            'tiktok' => 'required',
        ]);

        $settings = AdminSetting::first();
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

        return redirect()->route('admin.settings.social_media.edit')->with('success', 'Social media settings updated successfully.');
    }
}
