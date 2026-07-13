<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Actions\Admin\Settings\UpdateContactInfoSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\ContactInfoSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactInfoController extends Controller
{
    public function edit(GetAdminSettingsAction $settings): View
    {
        return view('admin.settings.contact-info', ['settings' => $settings->execute()]);
    }

    public function update(
        ContactInfoSettingsRequest $request,
        GetAdminSettingsAction $settings,
        UpdateContactInfoSettingsAction $action
    ): RedirectResponse {
        $action->execute($settings->execute(), $request->validated());

        return redirect()->route('admin.settings.contact-info.edit')
            ->with('success', 'Contact info updated successfully.');
    }
}
