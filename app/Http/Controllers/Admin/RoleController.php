<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Roles\CreateRoleAction;
use App\Actions\Admin\Roles\DeleteRoleAction;
use App\Actions\Admin\Roles\GetRoleDetailsAction;
use App\Actions\Admin\Roles\GetRoleManagementDataAction;
use App\Actions\Admin\Roles\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request, GetRoleManagementDataAction $action): View
    {
        return view('admin.role.index', $action->execute(auth('admin')->user(), $request->integer('edit') ?: null));
    }

    public function create(GetRoleManagementDataAction $action): View
    {
        return view('admin.role.index', $action->execute(auth('admin')->user()));
    }

    public function store(RoleRequest $request, CreateRoleAction $action): RedirectResponse
    {
        $action->execute(auth('admin')->user(), $request->validated());

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully with selected permissions.');
    }

    public function show(Role $role, GetRoleDetailsAction $action): View
    {
        return view('admin.role.show', ['role' => $action->execute(auth('admin')->user(), $role)]);
    }

    public function edit(Role $role, GetRoleManagementDataAction $action): View
    {
        return view('admin.role.index', $action->execute(auth('admin')->user(), $role));
    }

    public function update(
        RoleRequest $request,
        Role $role,
        UpdateRoleAction $action
    ): RedirectResponse {
        try {
            $action->execute(auth('admin')->user(), $role, $request->validated());
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully with selected permissions.');
    }

    public function destroy(Role $role, DeleteRoleAction $action): RedirectResponse
    {
        try {
            $action->execute(auth('admin')->user(), $role);
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
