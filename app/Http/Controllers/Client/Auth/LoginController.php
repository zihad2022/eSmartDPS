<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function login()
    {
        return view('client.auth.login');
    }

    /**
     * Handle login form submission.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ]);
        // Try to find client by username (case-sensitive match)
        $client = Client::where('user_id', $credentials['user_id'])->first();

        if (! $client) {
            // Username not found
            return back()->withErrors([
                'user_id' => 'No account found for this user ID.',
            ])->withInput();
        }

        if (! Hash::check($credentials['password'], $client->password)) {
            // Password doesn't match
            return back()->withErrors([
                'password' => 'The password you entered is incorrect.',
            ])->withInput();
        }

        if ($client->status == 0) {
            // Client is inactive
            return redirect()
                ->route('client.login')
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // Login success
        Auth::guard('client')->login($client);

        return redirect()->intended(route('client.dashboard'));
    }

    /**
     * Logout the authenticated client.
     */
    public function logout()
    {
        Auth::guard('client')->logout();

        return redirect()->route('landing');
    }
}
