<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;

class LedgerCategoryController extends Controller
{
    /**
     * Display a listing of the ledger categories.
     */
    public function index()
    {
        $clientId = auth('client')->id();

        $ledgerCategories = LedgerCategory::where('client_id', $clientId)
            ->latest()
            ->paginate(10);

        $activeCount = LedgerCategory::where('client_id', $clientId)->active()->count();
        $inactiveCount = LedgerCategory::where('client_id', $clientId)->inactive()->count();

        return view('client.ledger-category.index', compact(
            'ledgerCategories',
            'activeCount',
            'inactiveCount'
        ));
    }

    /**
     * Show the form for creating a new ledger category.
     */
    public function create()
    {
        return view('client.ledger-category.form');
    }

    /**
     * Store a newly created ledger category.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        LedgerCategory::create(array_merge($data, [
            'client_id' => auth('client')->id(),
        ]));

        return redirect()
            ->route('client.ledger-categories.index')
            ->with('success', 'Ledger category created successfully.');
    }

    /**
     * Show the form for editing a ledger category.
     */
    public function edit(LedgerCategory $ledgerCategory)
    {
        $this->authorizeOwner($ledgerCategory);
        $clientId = auth('client')->id();
        $ledgerCategories = LedgerCategory::where('client_id', $clientId)
            ->latest()
            ->paginate(10);

        return view('client.ledger-category.index', compact('ledgerCategories', 'ledgerCategory'));
    }

    /**
     * Update a ledger category.
     */
    public function update(Request $request, LedgerCategory $ledgerCategory)
    {
        $this->authorizeOwner($ledgerCategory);

        $ledgerCategory->update($this->validateRequest($request));

        return redirect()
            ->route('client.ledger-categories.index')
            ->with('success', 'Ledger category updated successfully.');
    }

    /**
     * Delete a ledger category.
     */
    public function destroy(LedgerCategory $ledgerCategory)
    {
        $this->authorizeOwner($ledgerCategory);

        $ledgerCategory->delete();

        return redirect()
            ->route('client.ledger-categories.index')
            ->with('success', 'Ledger category deleted successfully.');
    }

    /**
     * Validate the request for storing/updating.
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    }

    /**
     * Ensure the ledger category belongs to the logged-in client.
     */
    protected function authorizeOwner(LedgerCategory $category): void
    {
        if ($category->client_id !== auth('client')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
