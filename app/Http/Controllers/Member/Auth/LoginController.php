<?php

namespace App\Http\Controllers\Member\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login()
    {
        return view('member.auth.login');
    }

    public function authenticate(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'member_id' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find member by member_id
        $member = Member::where('member_id', $credentials['member_id'])->first();

        // Check if member exists and password is correct
        if (! $member || ! Hash::check($credentials['password'], $member->password)) {
            return redirect()->back()->with('error', 'Invalid credentials');
        }

        // Check if member is active
        // if ($member->status == 0) {
        //     return redirect()
        //         ->back()
        //         ->with('error', 'Your account is currently inactive. Please contact the administrator or support team for assistance.');
        // }

        // Log in the member
        Auth::guard('member')->login($member);

        // Redirect to dashboard
        return redirect()->intended(route('member.dashboard'));
    }

    /**
     * Logout the member.
     */
    public function logout()
    {
        Auth::guard('member')->logout();

        return redirect()->route('home');
    }
}
