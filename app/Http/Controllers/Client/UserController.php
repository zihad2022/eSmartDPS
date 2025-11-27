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
     * Display a listing of users under the authenticated parent client.
     */
    public function index(Request $request)
    {
        $parent = $this->getOwner();
        $query  = $parent->children()->newQuery();

        // Apply filters
        $this->applyFilters($query, $request);

        $users = $query->paginate(10);

        // Add parent client to results when required
        if ($this->shouldIncludeParent($parent, $request)) {
            $merged = collect([$parent])->merge($users->items());
            $users->setCollection($merged);
        }

        // Stats
        $allClients = collect([$parent])->merge($parent->children()->get());

        return view('client.user.index', [
            'users'              => $users,
            'totalUsers'         => $allClients->count(),
            'administratorUsers' => $allClients->where('role', 'admin')->count(),
            'managerUsers'       => $allClients->where('role', 'manager')->count(),
            'editorUsers'        => $allClients->where('role', 'editor')->count(),
            'activeUsers'        => $allClients->where('status', 1)->count(),
            'inactiveUsers'      => $allClients->where('status', 0)->count(),
        ]);
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

        Client::create(
            $this->prepareUserData($request)
        );

        return redirect()->route('client.users.index')
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

        $user->update(
            $this->prepareUserData($request, $user)
        );

        return redirect()->route('client.users.index')
            ->with('success', 'User has been updated successfully.');
    }

    public function destroy(Client $user)
    {
        $this->authorizeOwner($user);

        if ($user->role === 'super_admin') {
            return back()->with('error', 'You cannot delete a super admin.');
        }

        $this->deleteUserImages($user);

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

        if (!in_array($ownerId, [$user->id, $user->parent_id])) {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * Apply all filters to user listing.
     */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->status === 'active') {
            $query->where('status', 1);
        } elseif ($request->status === 'inactive') {
            $query->where('status', 0);
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $fields = [
                    'first_name', 'last_name', 'email', 'phone',
                    'user_id', 'nid_number', 'division', 'district',
                    'address', 'postal_code'
                ];

                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }
    }

    /**
     * Determine if the parent should appear in results.
     */
    private function shouldIncludeParent(Client $parent, Request $request): bool
    {
        // Role Filter
        if ($request->role && $parent->role !== $request->role) {
            return false;
        }

        // Status Filter
        if ($request->status === 'active' && !$parent->status) return false;
        if ($request->status === 'inactive' && $parent->status) return false;

        // Search Filter
        if ($search = $request->search) {
            $fields = [
                $parent->first_name, $parent->last_name, $parent->email,
                $parent->phone, $parent->user_id, $parent->nid_number,
                $parent->division, $parent->district, $parent->address,
                $parent->postal_code
            ];

            $match = collect($fields)
                ->filter()
                ->contains(fn($v) => str_contains(strtolower($v), strtolower($search)));

            if (!$match) return false;
        }

        return true;
    }

    /**
     * Prepare validated + transformed data for create/update.
     */
    private function prepareUserData(UserRequest $request, Client $user = null): array
    {
        $data = $request->validated();

        // User ID
        $data['user_id'] = $user?->user_id ?? generate_client_user_id();

        // Parent assignment
        $data['parent_id'] = owner_client_id();

        // Password handling
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // Image uploads
        $this->processUploads($request, $user, $data);

        return $data;
    }

    /**
     * Handle image uploads for profile photo & NID.
     */
    private function processUploads(UserRequest $request, ?Client $user, array &$data): void
    {
        $uploads = [
            'profile_photo',
            'nid_card_front',
            'nid_card_back',
        ];

        foreach ($uploads as $field) {
            if ($request->hasFile($field)) {
                $this->uploadAndReplace($request, $user, $data, $field);
            }
        }
    }

    private function uploadAndReplace(Request $request, ?Client $user, array &$data, string $field): void
    {
        if ($user?->$field) {
            $this->imageService->deleteImage($user->$field);
        }

        $data[$field] = $this->imageService->uploadImage(
            $request->file($field),
            'uploads/clients/users'
        );
    }

    /**
     * Delete all images for user before deletion.
     */
    private function deleteUserImages(Client $user): void
    {
        foreach (['profile_photo', 'nid_card_front', 'nid_card_back'] as $field) {
            if ($user->$field) {
                $this->imageService->deleteImage($user->$field);
            }
        }
    }
}
