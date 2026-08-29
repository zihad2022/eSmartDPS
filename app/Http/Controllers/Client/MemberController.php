<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\MemberRequest;
use App\Models\Client;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a paginated list of members with optional status filter.
     */
    public function index(Request $request)
    {
        $ownerId = owner_client_id();

        // -----------------------------
        // 1. Capture search query
        // -----------------------------
        $search = $request->get('search');

        // -----------------------------
        // 2. Fetch members for the current client with search & status filters
        // -----------------------------
        $members = Member::query()
            ->where('client_id', $ownerId)
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('member_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->status === 'active', fn ($q) => $q->where('status', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('status', false))
            ->select('id', 'member_id', 'name', 'profile_photo', 'email', 'phone', 'status', 'share_quantity', 'total_balance', 'created_at')
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        // -----------------------------
        // 3. Calculate summary stats
        // -----------------------------
        $summary = Member::where('client_id', $ownerId)
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('member_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->selectRaw('COUNT(*) as total, 
                         SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active, 
                         SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive, 
                         SUM(share_quantity) as total_shares')
            ->first();

        // -----------------------------
        // 4. Fetch client settings
        // -----------------------------
        $settings = ClientSetting::where('client_id', $ownerId)->first();

        // -----------------------------
        // 5. Return view
        // -----------------------------
        return view('client.member.index', [
            'members' => $members,
            'totalMembers' => $summary->total ?? 0,
            'activeMembers' => $summary->active ?? 0,
            'inactiveMembers' => $summary->inactive ?? 0,
            'totalShares' => $summary->total_shares ?? 0,
            'settings' => $settings,
            'search' => $search, // Keep search input populated
        ]);
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        // -----------------------------
        // 1. Generate a new member ID
        // -----------------------------
        return view('client.member.form', [
            'memberId' => generate_member_id(),
        ]);
    }

    /**
     * Store a newly created member.
     */
    public function store(MemberRequest $request)
    {
        $client = Client::findOrFail(owner_client_id());

        // -----------------------------
        // 1. Check package limit for members
        // -----------------------------
        if (! $client->canAddMember()) {
            return back()->with('error', 'You have reached the maximum limit of members for your package.');
        }

        // -----------------------------
        // 2. Prepare member data
        // -----------------------------
        $data = [
            'member_id' => generate_member_id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'share_quantity' => $request->share_quantity ?? 0,
            'password' => Hash::make($request->password),
        ];

        // -----------------------------
        // 3. Handle profile photo upload
        // -----------------------------
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $this->imageService->uploadImage(
                $request->file('profile_photo'),
                'uploads/clients/'.owner_client_id().'/members'
            );
        }

        // -----------------------------
        // 4. Create member
        // -----------------------------
        $client->members()->create($data);

        // -----------------------------
        // 5. Redirect with success
        // -----------------------------
        return redirect()
            ->route('client.members.index')
            ->with('success', 'Member has been added successfully.');
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member)
    {
        authorize_owner($member);

        $settings = ClientSetting::where('client_id', owner_client_id())->first();

        return view('client.member.show', compact('member', 'settings'));
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member)
    {
        authorize_owner($member);

        return view('client.member.form', compact('member'));
    }

    /**
     * Update the specified member.
     */
    public function update(MemberRequest $request, Member $member)
    {
        authorize_owner($member);

        // -----------------------------
        // 1. Prepare update data
        // -----------------------------
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'share_quantity' => $request->share_quantity ?? $member->share_quantity,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $this->imageService->uploadImage(
                $request->file('profile_photo'),
                'uploads/clients/'.owner_client_id().'/members'
            );
        }

        // -----------------------------
        // 2. Update member
        // -----------------------------
        $member->update($data);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()
            ->route('client.members.index')
            ->with('success', 'Member has been updated successfully.');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Member $member)
    {
        authorize_owner($member);

        $this->imageService->deleteImage($member->profile_photo);
        // -----------------------------
        // 1. Delete member
        // -----------------------------
        $member->delete();

        // -----------------------------
        // 2. Redirect with success
        // -----------------------------
        return redirect()
            ->route('client.members.index')
            ->with('success', 'Member has been deleted successfully.');
    }
}
