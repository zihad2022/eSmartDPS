<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UserRequest;
use App\Models\Client;
use App\Services\ImageService;

class UserController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    /** ✅ Show All Users */
    public function index()
    {
        $parentId = owner_client_id(); // Still use the helper for parent ID
        $parent = Client::findOrFail($parentId);

        $users = $parent->children()->get()->prepend($parent);

        return view('client.user.index', compact('users'));
    }

    public function create()
    {
        return view('client.user.form', [
            'user_id' => generate_client_user_id(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $validated = $this->prepareUserData($request);

        Client::create($validated);

        return to_route('client.users.index')
            ->with('success', 'User has been added successfully.');
    }

    public function edit(Client $user)
    {
        $this->authorizeOwner($user);

        return view('client.user.form', compact('user'));
    }

    public function show(Client $user)
    {
        $this->authorizeOwner($user);

        return view('client.user.show', compact('user'));
    }

    public function update(UserRequest $request, Client $user)
    {
        $this->authorizeOwner($user);

        $validated = $this->prepareUserData($request, $user);

        $user->update($validated);

        return to_route('client.users.index')
            ->with('success', 'User has been updated successfully.');
    }

    /** ✅ Authorization Logic */
    private function authorizeOwner(Client $user): void
    {
        $ownerId = owner_client_id(); // Get parent ID

        if ($user->id !== $ownerId && $user->parent_id !== $ownerId) {
            abort(403, 'Unauthorized access');
        }
    }

    /** ✅ Prepare validated data (handle password + image) */
    private function prepareUserData(UserRequest $request, Client $user = null): array
    {
        $data = $request->validated();

        if ($request->hasFile('profile_photo')) {
            if ($user?->profile_photo) {
                $this->imageService->deleteImage($user->profile_photo);
            }

            $data['profile_photo'] = $this->imageService->uploadImage(
                $request->file('profile_photo'),
                'uploads/clients/users'
            );
        }

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }
}
