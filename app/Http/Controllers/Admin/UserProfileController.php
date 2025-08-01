<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    /** Show Profile Edit Page */
    public function edit()
    {
        return view('admin.user.profile', [
            'user' => Auth::guard('admin')->user(),
        ]);
    }

    /** Update Profile */
    public function update(Request $request)
    {
        $user = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:6',
        ]);

        // Handle Profile Photo Upload
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                $this->imageService->deleteImage($user->profile_photo);
            }

            $validated['profile_photo'] = $this->imageService->uploadImage(
                $request->file('profile_photo'),
                'uploads/admins/users'
            );
        }

        // Handle Password (only if provided)
        if (! empty($validated['password'])) {
            $validated['password'] = $validated['password'];
        } else {
            unset($validated['password']);
        }

        // Prevent updating restricted fields
        unset($validated['email']);
        unset($validated['role']);
        unset($validated['status']);

        $user->update($validated);

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
