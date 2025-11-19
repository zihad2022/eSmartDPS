<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Domain\Clients\Models\Client;
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
        // -----------------------------
        // 1. Display login view
        // -----------------------------
        // Return the Blade view for the client login form.
        return view('client.auth.login');
    }

    /**
     * Handle login form submission.
     */
    public function authenticate(Request $request)
    {
        // -----------------------------
        // 1. Validate request inputs
        // -----------------------------
        // Ensure both user_id and password are provided.
        $credentials = $request->validate([
            'user_id' => 'required|string',
            'password' => 'required|string',
        ]);

        // -----------------------------
        // 2. Retrieve client by user ID
        // -----------------------------
        // Attempt to find a client with the provided user_id.
        $client = Client::where('user_id', $credentials['user_id'])->first();

        // -----------------------------
        // 3. Handle non-existent user
        // -----------------------------
        if (! $client) {
            return back()->withErrors([
                'user_id' => 'No client account found for this User ID.',
            ])->withInput();
        }

        // -----------------------------
        // 4. Verify password
        // -----------------------------
        // Check if the entered password matches the stored hashed password.
        if (! Hash::check($credentials['password'], $client->password)) {
            return back()->withErrors([
                'password' => 'Incorrect password entered.',
            ])->withInput();
        }

        // -----------------------------
        // 5. Check client status
        // -----------------------------
        // Prevent login if the account is inactive.
        if (! $client->status) {
            return redirect()
                ->route('client.login')
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        // -----------------------------
        // 6. Authenticate the client
        // -----------------------------
        Auth::guard('client')->login($client);

        // -----------------------------
        // 7. Redirect to intended page
        // -----------------------------
        // Redirect to the dashboard or the originally intended URL.
        return redirect()->intended(route('client.dashboard'));
    }

    /**
     * Logout the authenticated client.
     */
    public function logout()
    {
        // -----------------------------
        // 1. Logout the client
        // -----------------------------
        Auth::guard('client')->logout();

        // -----------------------------
        // 2. Redirect to login page
        // -----------------------------
        return redirect()->route('client.login');
    }
}
