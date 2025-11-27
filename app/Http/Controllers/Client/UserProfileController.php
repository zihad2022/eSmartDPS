<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function edit()
    {
        return view('client.user.profile', [
            'client' => Auth::guard('client')->user(),
        ]);
    }

    public function update(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'phone'             => 'required|string|max:255',
            'profile_photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password'  => 'nullable|required_with:new_password|string',
            'new_password'      => 'nullable|string|min:6',
        ]);

        // Profile photo
        if ($request->hasFile('profile_photo')) {
            if ($client->profile_photo) {
                $this->imageService->deleteImage($client->profile_photo);
            }

            $validated['profile_photo'] = $this->imageService->uploadImage(
                $request->file('profile_photo'),
                'uploads/clients/users'
            );
        }

        // Password update
        if (!empty($validated['new_password'])) {

            // Verify current password
            if (!Hash::check($validated['current_password'], $client->password)) {
                return back()->withErrors([
                    'current_password' => 'Your current password is incorrect.'
                ]);
            }

            // Logout from other devices
            Auth::guard('client')->logoutOtherDevices($validated['current_password']);

            // Save new password
            $validated['password'] = Hash::make($validated['new_password']);
        }

        // Remove unnecessary fields
        unset(
            $validated['email'],
            $validated['role'],
            $validated['status'],
            $validated['current_password'],
            $validated['new_password']
        );

        $client->update($validated);

        return redirect()
            ->route('client.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
