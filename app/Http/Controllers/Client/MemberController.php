<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\MemberRequest;
use App\Models\Client;
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
     * Display a listing of the members.
     */
    public function index(Request $request)
    {
        $ownerId = owner_client_id();

        $members = Member::query()
            ->where('client_id', $ownerId)
            ->select('id', 'member_id', 'name', 'profile_photo', 'email', 'phone', 'status', 'share_quantity', 'total_balance', 'created_at')
            ->when($request->status === 'active', fn ($q) => $q->where('status', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('status', false))
            ->latest()
            ->paginate(10);

        $summary = Member::where('client_id', $ownerId)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active, SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive, SUM(share_quantity) as total_shares')
            ->first();

        return view('client.member.index', [
            'members' => $members,
            'totalMembers' => $summary->total,
            'activeMembers' => $summary->active,
            'inactiveMembers' => $summary->inactive,
            'totalShares' => $summary->total_shares,
        ]);
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
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

        if (! $client->canAddMember()) {
            return back()->with('error', 'You have reached the maximum limit of members for your package.');
        }

        $data = [
            'member_id' => generate_member_id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'share_quantity' => $request->share_quantity ?? 0,
            'total_balance' => $request->total_balance ?? 0,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $this->imageService->uploadImage(
                $request->file('profile_photo'),
                'uploads/clients/'.owner_client_id().'/members'
            );
        }

        $client->members()->create($data);

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

        return view('client.member.show', compact('member'));
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

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'share_quantity' => $request->share_quantity ?? $member->share_quantity,
            'total_balance' => $request->total_balance ?? $member->total_balance,
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

        $member->update($data);

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

        $member->delete();

        return redirect()
            ->route('client.members.index')
            ->with('success', 'Member has been deleted successfully.');
    }
}
