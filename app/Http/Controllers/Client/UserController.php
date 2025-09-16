<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UserRequest;
use App\Models\Client;
use App\Services\ImageService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    /**
     * Display a paginated list of users (children) with optional role/status filters.
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1. Fetch owner (parent client)
        // -----------------------------
        $parent = Client::findOrFail(owner_client_id());
    
        // -----------------------------
        // 2. Build children query
        // -----------------------------
        $query = $parent->children();
    
        // Apply optional role filter
        if ($request->role) {
            $query->where('role', $request->role);
        }
    
        // Apply optional status filter
        if ($request->status === 'active') {
            $query->where('status', 1);
        } elseif ($request->status === 'inactive') {
            $query->where('status', 0);
        }
    
        // -----------------------------
        // 3. Apply search filter
        // -----------------------------
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('user_id', 'like', "%{$search}%")
                  ->orWhere('nid_number', 'like', "%{$search}%")
                  ->orWhere('division', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('postal_code', 'like', "%{$search}%");
            });
        }
    
        // -----------------------------
        // 4. Paginate results
        // -----------------------------
        $users = $query->paginate(10);
    
        // -----------------------------
        // 5. Include parent if it matches filters
        // -----------------------------
        $includeParent = true;
    
        if ($request->role && $parent->role !== $request->role) $includeParent = false;
        if ($request->status === 'active' && !$parent->status) $includeParent = false;
        if ($request->status === 'inactive' && $parent->status) $includeParent = false;
    
        if ($request->search) {
            // check if parent matches search
            $matchesSearch = str_contains(strtolower($parent->first_name), strtolower($request->search))
                || str_contains(strtolower($parent->last_name), strtolower($request->search))
                || str_contains(strtolower($parent->email), strtolower($request->search))
                || str_contains(strtolower($parent->phone ?? ''), strtolower($request->search))
                || str_contains(strtolower($parent->user_id), strtolower($request->search))
                || str_contains(strtolower($parent->nid_number ?? ''), strtolower($request->search))
                || str_contains(strtolower($parent->division ?? ''), strtolower($request->search))
                || str_contains(strtolower($parent->district ?? ''), strtolower($request->search))
                || str_contains(strtolower($parent->address ?? ''), strtolower($request->search))
                || str_contains(strtolower($parent->postal_code ?? ''), strtolower($request->search));
    
            if (!$matchesSearch) {
                $includeParent = false;
            }
        }
    
        if ($includeParent) {
            $usersCollection = collect([$parent])->merge($users->items());
            $users->setCollection($usersCollection);
        }
    
        // -----------------------------
        // 6. Calculate stats
        // -----------------------------
        $collection = $users->getCollection();
        $totalUsers = $collection->count();
        $administratorUsers = $collection->where('role', 'admin')->count();
        $managerUsers = $collection->where('role', 'manager')->count();
        $editorUsers = $collection->where('role', 'editor')->count();
        $activeUsers = $collection->where('status', 1)->count();
        $inactiveUsers = $collection->where('status', 0)->count();
    
        // -----------------------------
        // 7. Return view
        // -----------------------------
        return view('client.user.index', compact(
            'users', 'totalUsers', 'administratorUsers', 'managerUsers',
            'editorUsers', 'activeUsers', 'inactiveUsers'
        ));
    }
    

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        return view('client.user.form', [
            'user_id' => generate_client_user_id(),
        ]);
    }

    /**
     * Store a newly created user in the database.
     */
    public function store(UserRequest $request)
    {
        // -----------------------------
        // 1. Get owner
        // -----------------------------
        $client = $this->getOwner();

        // -----------------------------
        // 2. Check package limit
        // -----------------------------
        if (!$client->canAddUser()) {
            return back()->with('error', 'You have reached the maximum user limit for your package.');
        }

        // -----------------------------
        // 2. Create user
        // -----------------------------
        Client::create($this->prepareUserData($request));

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return to_route('client.users.index')
            ->with('success', 'User has been added successfully.');
    }

    /**
     * Show the form to edit an existing user.
     */
    public function edit(Client $user)
    {
        // -----------------------------
        // 1. Authorize owner
        // -----------------------------
        $this->authorizeOwner($user);

        // -----------------------------
        // 2. Return view
        // -----------------------------
        return view('client.user.form', compact('user'));
    }

    /**
     * Display a single user's details.
     */
    public function show(Client $user)
    {
        // -----------------------------
        // 1. Authorize owner
        // -----------------------------
        $this->authorizeOwner($user);

        // -----------------------------
        // 2. Return view
        // -----------------------------
        return view('client.user.show', compact('user'));
    }

    /**
     * Update an existing user.
     */
    public function update(UserRequest $request, Client $user)
    {
        // -----------------------------
        // 1. Authorize owner
        // -----------------------------
        $this->authorizeOwner($user);

        // -----------------------------
        // 2. Update user
        // -----------------------------
        $user->update($this->prepareUserData($request, $user));

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return to_route('client.users.index')
            ->with('success', 'User has been updated successfully.');
    }

    /**
     * Delete a user.
     */
    public function destroy(Client $user)
    {
        // -----------------------------
        // 1. Authorize owner
        // -----------------------------
        $this->authorizeOwner($user);

        // -----------------------------
        // 2. Check if user is super admin
        // -----------------------------
        if ($user->role === 'super_admin') {
            return back()->with('error', 'You cannot delete super admin.');
        }

        // -----------------------------
        // 3. Delete user
        // -----------------------------
        $user->delete();

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
        return redirect()->route('client.users.index')
            ->with('success', 'User has been deleted successfully.');
    }

    // -------------------- Helpers --------------------

    /**
     * Get the owner (parent client).
     */
    private function getOwner(): Client
    {
        // -----------------------------
        // 1. Get owner ID
        // -----------------------------
        return Client::findOrFail(owner_client_id());
    }

    /**
     * Authorize if the current user is owner or child of owner.
     */
    private function authorizeOwner(Client $user): void
    {
        // -----------------------------
        // 1. Get owner ID
        // -----------------------------
        $ownerId = owner_client_id();

        // -----------------------------
        // 2. Check authorization
        // -----------------------------
        if ($user->id !== $ownerId && $user->parent_id !== $ownerId) {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Prepare validated user data (password, image, parent_id).
     */
    private function prepareUserData(UserRequest $request, Client $user = null): array
    {
        // -----------------------------
        // 1. Validate and prepare data
        // -----------------------------
        $data = $request->validated();

        // -----------------------------
        // 2. Handle profile photo
        // -----------------------------
        if ($request->hasFile('profile_photo')) {
            $this->handleProfilePhoto($request, $user, $data);
        }

        // -----------------------------
        // 3. Set parent ID
        // -----------------------------
        $data['parent_id'] = owner_client_id();

        // -----------------------------
        // 4. Handle password
        // -----------------------------
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // -----------------------------
        // 5. Return prepared data
        // -----------------------------
        return $data;
    }

    /**
     * Handle profile photo upload and deletion.
     */
    private function handleProfilePhoto(UserRequest $request, ?Client $user, array &$data): void
    {
        // -----------------------------
        // 1. Delete existing photo if exists
        // -----------------------------
        if ($user?->profile_photo) {
            $this->imageService->deleteImage($user->profile_photo);
        }

        // -----------------------------
        // 2. Upload new photo
        // -----------------------------
        $data['profile_photo'] = $this->imageService->uploadImage(
            $request->file('profile_photo'),
            'uploads/clients/users'
        );
    }
}
