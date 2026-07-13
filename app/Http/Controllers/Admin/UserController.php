<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Users\CreateAdminAction;
use App\Actions\Admin\Users\DeleteAdminAction;
use App\Actions\Admin\Users\GetAdminDetailsAction;
use App\Actions\Admin\Users\GetAdminFormDataAction;
use App\Actions\Admin\Users\GetAdminsAction;
use App\Actions\Admin\Users\UpdateAdminAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request, GetAdminsAction $action): View
    {
        return view('admin.user.index', $action->execute(
            actor: auth('admin')->user(),
            search: $request->string('search')->trim()->toString() ?: null,
            status: $request->string('status')->toString() ?: null,
        ));
    }

    public function create(GetAdminFormDataAction $action): View
    {
        return view('admin.user.form', $action->execute(auth('admin')->user()));
    }

    public function store(UserRequest $request, CreateAdminAction $action): RedirectResponse
    {
        $action->execute(auth('admin')->user(), $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin has been added successfully.');
    }

    public function show(Admin $user, GetAdminDetailsAction $action): View
    {
        return view('admin.user.show', $action->execute(auth('admin')->user(), $user));
    }

    public function edit(Admin $user, GetAdminFormDataAction $action): View
    {
        return view('admin.user.form', $action->execute(auth('admin')->user(), $user));
    }

    public function update(
        UserRequest $request,
        Admin $user,
        UpdateAdminAction $action
    ): RedirectResponse {
        $action->execute(auth('admin')->user(), $user, $request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin has been updated successfully.');
    }

    public function destroy(Admin $user, DeleteAdminAction $action): RedirectResponse
    {
        try {
            $action->execute(auth('admin')->user(), $user);
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin has been deleted successfully.');
    }
}
