<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Profiles\GetAdminProfileDataAction;
use App\Actions\Admin\Profiles\UpdateAdminProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminProfileRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    public function edit(GetAdminProfileDataAction $action): View
    {
        /** @var Admin $admin */
        $admin = auth('admin')->user();

        return view('admin.user.profile', $action->execute($admin));
    }

    public function update(
        AdminProfileRequest $request,
        UpdateAdminProfileAction $action
    ): RedirectResponse {
        /** @var Admin $admin */
        $admin = auth('admin')->user();
        $action->execute($admin, $request->validated());

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
