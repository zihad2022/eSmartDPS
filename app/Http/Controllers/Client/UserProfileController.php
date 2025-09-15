<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    /**
     * Show the form to edit the authenticated user's profile.
     */
    public function edit()
    {
        // -----------------------------
        // 1. Return profile view
        // -----------------------------
        return view('client.user.profile', [
            'client' => Auth::guard('client')->user(),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        // -----------------------------
        // 1. Get authenticated client
        // -----------------------------
        $client = Auth::guard('client')->user();

        // -----------------------------
        // 2. Validate request
        // -----------------------------
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        // -----------------------------
        // 3. Handle Profile Photo Upload
        // -----------------------------
        if ($request->hasFile('profile_photo')) {
            if ($client->profile_photo) {
                $this->imageService->deleteImage($client->profile_photo);
            }

            $validated['profile_photo'] = $this->imageService->uploadImage(
                $request->file('profile_photo'),
                'uploads/clients/users'
            );
        }

        // -----------------------------
        // 4. Handle Password (only if provided)
        // -----------------------------
        if (! empty($validated['password'])) {
            $validated['password'] = $validated['password'];
        } else {
            unset($validated['password']);
        }

        // -----------------------------
        // 5. Prevent updating restricted fields
        // -----------------------------
        unset($validated['email']);
        unset($validated['role']);
        unset($validated['status']);

        // -----------------------------
        // 6. Update client
        // -----------------------------
        $client->update($validated);

        // -----------------------------
        // 7. Redirect with success message
        // -----------------------------
        return redirect()
            ->route('client.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
