<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Actions\Admin\Settings\UpdatePaymentSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\PaymentSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function edit(GetAdminSettingsAction $settings): View
    {
        return view('admin.settings.payment', ['settings' => $settings->execute()]);
    }

    public function update(
        PaymentSettingsRequest $request,
        GetAdminSettingsAction $settings,
        UpdatePaymentSettingsAction $action
    ): RedirectResponse {
        $section = $request->string('section')->toString();
        $action->execute($settings->execute(), $section, $request->validated());

        return redirect()->route('admin.settings.payments.edit')
            ->with('success', ucfirst($section).' payment settings updated successfully.');
    }
}
