<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Actions\Admin\Settings\UpdateSocialMediaSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\SocialMediaSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SocialMediaController extends Controller
{
    public function edit(GetAdminSettingsAction $settings): View
    {
        return view('admin.settings.social-media', ['settings' => $settings->execute()]);
    }

    public function update(
        SocialMediaSettingsRequest $request,
        GetAdminSettingsAction $settings,
        UpdateSocialMediaSettingsAction $action
    ): RedirectResponse {
        $action->execute($settings->execute(), $request->validated());

        return redirect()->route('admin.settings.social-media.edit')
            ->with('success', 'Social media settings updated successfully.');
    }
}
