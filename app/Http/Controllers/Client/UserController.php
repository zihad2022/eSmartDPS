<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UserRequest;
use App\Models\Client;
use App\Services\ImageService;

class UserController extends Controller
{
    public function __construct(
        protected ImageService $imageService
    ) {
    }

    /** Show All Users */
    public function index()
    {
        $parent = Client::findOrFail(owner_client_id());

        $users = $parent->children()->get()->prepend($parent);

        return view('client.user.index', compact('users'));
    }

    /** Show Create Form */
    public function create()
    {
        return view('client.user.form', [
            'user_id' => generate_client_user_id(),
        ]);
    }

    /** Store New User */
    public function store(UserRequest $request)
    {
        $client = $this->getOwner();

        if (! $client->canAddChild()) {
            return back()->with('error', 'You have reached the maximum user limit for your package.');
        }

        Client::create($this->prepareUserData($request));

        return to_route('client.users.index')
            ->with('success', 'User has been added successfully.');
    }

    /** Edit User */
    public function edit(Client $user)
    {
        $this->authorizeOwner($user);

        return view('client.user.form', compact('user'));
    }

    /** Show User */
    public function show(Client $user)
    {
        $this->authorizeOwner($user);

        return view('client.user.show', compact('user'));
    }

    /** Update User */
    public function update(UserRequest $request, Client $user)
    {
        $this->authorizeOwner($user);

        $user->update($this->prepareUserData($request, $user));

        return to_route('client.users.index')
            ->with('success', 'User has been updated successfully.');
    }

    /** -------------------- Helpers -------------------- */

    /** Get the parent/owner client */
    private function getOwner(): Client
    {
        return Client::findOrFail(owner_client_id());
    }

    /** Authorization Logic */
    private function authorizeOwner(Client $user): void
    {
        $ownerId = owner_client_id();

        if ($user->id !== $ownerId && $user->parent_id !== $ownerId) {
            abort(403, 'Unauthorized access');
        }
    }

    /** Prepare validated data (password + image + parent_id) */
    private function prepareUserData(UserRequest $request, Client $user = null): array
    {
        $data = $request->validated();

        if ($request->hasFile('profile_photo')) {
            $this->handleProfilePhoto($request, $user, $data);
        }

        $data['parent_id'] = owner_client_id();

        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    /** Handle profile photo upload */
    private function handleProfilePhoto(UserRequest $request, ?Client $user, array &$data): void
    {
        if ($user?->profile_photo) {
            $this->imageService->deleteImage($user->profile_photo);
        }

        $data['profile_photo'] = $this->imageService->uploadImage(
            $request->file('profile_photo'),
            'uploads/clients/users'
        );
    }
}
