<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Actions\Admin\Auth\AuthenticateAdminAction;
use App\Actions\Admin\Auth\LogoutAdminAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function login(): View|RedirectResponse
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function authenticate(
        AdminLoginRequest $request,
        AuthenticateAdminAction $action
    ): RedirectResponse {
        $action->execute(
            request: $request,
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            remember: $request->boolean('remember'),
        );

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request, LogoutAdminAction $action): RedirectResponse
    {
        $action->execute($request);

        return redirect()->route('admin.login');
    }
}
