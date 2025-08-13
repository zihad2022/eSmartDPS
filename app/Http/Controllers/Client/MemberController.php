<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\MemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = owner_client_id();

        $members = Member::query()
            ->where('client_id', $ownerId)
            ->select('id', 'name', 'email', 'phone', 'status', 'created_at')
            ->when($request->status === 'active', fn ($q) => $q->where('status', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('status', false))
            ->latest()
            ->paginate(10);

        return view('client.member.index', [
            'members' => $members,
            'totalMembers' => Member::where('client_id', $ownerId)->count(),
            'activeMembers' => Member::where('client_id', $ownerId)->where('status', true)->count(),
            'inactiveMembers' => Member::where('client_id', $ownerId)->where('status', false)->count(),
            'totalShares' => Member::where('client_id', $ownerId)->sum('share_quantity'),
        ]);
    }

    public function create()
    {
        return view('client.member.form', [
            'memberId' => generate_member_id(),
        ]);
    }

    public function store(MemberRequest $request)
    {
        Member::create([
            'client_id' => owner_client_id(),
            'member_id' => generate_member_id(),
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'password' => Hash::make($request['password']),
            'status' => $request['status'],
        ]);

        return redirect()
            ->route('client.members.index')
            ->with('success', 'Member has been added successfully.');
    }

    public function edit(Member $member)
    {
        authorize_owner($member);

        return view('client.member.form', [
            'member' => $member,
        ]);
    }

    public function update(MemberRequest $request, Member $member)
    {
        authorize_owner($member);

        $member->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'password' => $request['password']
                ? Hash::make($request['password'])
                : $member->password,
            'status' => $request['status'],
        ]);

        return redirect()
            ->route('client.members.index')
            ->with('success', 'Member has been updated successfully.');
    }
}
