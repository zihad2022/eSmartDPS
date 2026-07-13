<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Actions\Admin\Settings\UpdateSmsSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\SmsSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SmsController extends Controller
{
    public function edit(GetAdminSettingsAction $settings): View
    {
        return view('admin.settings.sms', ['settings' => $settings->execute()]);
    }

    public function update(
        SmsSettingsRequest $request,
        GetAdminSettingsAction $settings,
        UpdateSmsSettingsAction $action
    ): RedirectResponse {
        $action->execute($settings->execute(), $request->validated());

        return redirect()->route('admin.settings.sms.edit')
            ->with('success', 'SMS settings updated successfully.');
    }
}
