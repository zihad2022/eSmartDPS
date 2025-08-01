<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ShareRequest;
use App\Models\Share;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    /**
     * Display a listing of shares.
     */
    public function index(Request $request)
    {
        $shares = Share::where('client_id', owner_client_id())
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $totalShares = Share::where('client_id', owner_client_id())->count();
        $activeShares = Share::where('client_id', owner_client_id())->where('is_active', true)->count();
        $inactiveShares = Share::where('client_id', owner_client_id())->where('is_active', false)->count();

        return view('client.share.index', [
            'shares' => $shares,
            'totalShares' => $totalShares,
            'activeShares' => $activeShares,
            'inactiveShares' => $inactiveShares,
        ]);
    }

    /**
     * Show the form for creating a new share.
     */
    public function create()
    {
        return view('client.share.form');
    }

    /**
     * Store a newly created share in storage.
     */
    public function store(ShareRequest $request)
    {
        Share::create([
            ...$request->validated(),
            'client_id' => owner_client_id(),
        ]);

        return redirect()
            ->route('client.shares.index')
            ->with('success', 'Share plan created successfully.');
    }

    /**
     * Show the form for editing the specified share.
     */
    public function edit(Share $share)
    {
        authorize_owner($share);

        return view('client.share.form', [
            'share' => $share,
        ]);
    }

    /**
     * Update the specified share in storage.
     */
    public function update(ShareRequest $request, Share $share)
    {
        authorize_owner($share);

        $share->update($request->validated());

        return redirect()
            ->route('client.shares.index')
            ->with('success', 'Share plan updated successfully.');
    }

    /**
     * Remove the specified share from storage.
     */
    public function destroy(Share $share)
    {
        authorize_owner($share);

        $share->delete();

        return redirect()
            ->route('client.shares.index')
            ->with('success', 'Share plan deleted successfully.');
    }
}
