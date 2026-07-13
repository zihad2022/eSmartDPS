<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Actions\Admin\Settings\UpdateGeneralSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\GeneralSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeneralController extends Controller
{
    public function edit(GetAdminSettingsAction $settings): View
    {
        return view('admin.settings.general', ['settings' => $settings->execute()]);
    }

    public function update(
        GeneralSettingsRequest $request,
        GetAdminSettingsAction $settings,
        UpdateGeneralSettingsAction $action
    ): RedirectResponse {
        $action->execute($settings->execute(), $request->validated());

        return redirect()->route('admin.settings.general.edit')
            ->with('success', 'General settings updated successfully.');
    }
}
