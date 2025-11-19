<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UserRequest;
use App\Domain\Clients\Models\Client;
use App\Services\ImageService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    /**
     * List all users under the current client with filters.
     */
    public function index(Request $request)
    {
        $parent = Client::findOrFail(owner_client_id());
        $query  = $parent->children();

        // Filters
        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status === 'active') {
            $query->where('status', 1);
        } elseif ($request->status === 'inactive') {
            $query->where('status', 0);
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                foreach ([
                    'first_name', 'last_name', 'email', 'phone',
                    'user_id', 'nid_number', 'division', 'district',
                    'address', 'postal_code'
                ] as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        $users = $query->paginate(10);

        // Include parent client if matches filter
        $includeParent = $this->shouldIncludeParent($parent, $request, $search);

        if ($includeParent) {
            $merged = collect([$parent])->merge($users->items());
            $users->setCollection($merged);
        }

        // Stats
        $allClients        = collect([$parent])->merge($parent->children()->get());
        $totalUsers        = $allClients->count();
        $administratorUsers = $allClients->where('role', 'admin')->count();
        $managerUsers      = $allClients->where('role', 'manager')->count();
        $editorUsers       = $allClients->where('role', 'editor')->count();
        $activeUsers       = $allClients->where('status', 1)->count();
        $inactiveUsers     = $allClients->where('status', 0)->count();

        return view('client.user.index', compact(
            'users', 'totalUsers', 'administratorUsers',
            'managerUsers', 'editorUsers', 'activeUsers', 'inactiveUsers'
        ));
    }

    public function create()
    {
        return view('client.user.form', [
            'user_id' => generate_client_user_id(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $client = $this->getOwner();

        if (!$client->canAddUser()) {
            return back()->with('error', 'You have reached the maximum user limit for your package.');
        }

        Client::create($this->prepareUserData($request));

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

        $user->update($this->prepareUserData($request, $user));

        return to_route('client.users.index')
            ->with('success', 'User has been updated successfully.');
    }

    public function destroy(Client $user)
    {
        $this->authorizeOwner($user);

        if ($user->role === 'super_admin') {
            return back()->with('error', 'You cannot delete super admin.');
        }

        $user->delete();

        return redirect()->route('client.users.index')
            ->with('success', 'User has been deleted successfully.');
    }

    // -------------------- Helpers --------------------

    private function getOwner(): Client
    {
        return Client::findOrFail(owner_client_id());
    }

    private function authorizeOwner(Client $user): void
    {
        $ownerId = owner_client_id();

        if ($user->id !== $ownerId && $user->parent_id !== $ownerId) {
            abort(403, 'Unauthorized access');
        }
    }

    private function prepareUserData(UserRequest $request, Client $user = null): array
    {
        $data = $request->validated();

        if ($request->hasFile('profile_photo')) {
            $this->handleProfilePhoto($request, $user, $data);
        }

        $data['parent_id'] = owner_client_id();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

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

    private function shouldIncludeParent(Client $parent, Request $request, ?string $search): bool
    {
        $include = true;

        if ($request->role && $parent->role !== $request->role) $include = false;
        if ($request->status === 'active' && !$parent->status) $include = false;
        if ($request->status === 'inactive' && $parent->status) $include = false;

        if ($search) {
            $matches = collect([
                $parent->first_name, $parent->last_name, $parent->email,
                $parent->phone, $parent->user_id, $parent->nid_number,
                $parent->division, $parent->district, $parent->address,
                $parent->postal_code
            ])->filter()->contains(fn($v) => str_contains(strtolower($v), strtolower($search)));

            if (!$matches) $include = false;
        }

        return $include;
    }
}
