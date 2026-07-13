<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Actions\Admin\Settings\UpdateEmailSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\EmailSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function edit(GetAdminSettingsAction $settings): View
    {
        return view('admin.settings.email', ['settings' => $settings->execute()]);
    }

    public function update(
        EmailSettingsRequest $request,
        GetAdminSettingsAction $settings,
        UpdateEmailSettingsAction $action
    ): RedirectResponse {
        $action->execute($settings->execute(), $request->validated());

        return redirect()->route('admin.settings.email.edit')
            ->with('success', 'Email settings updated successfully.');
    }
}
