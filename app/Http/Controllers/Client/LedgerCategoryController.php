<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;

/**
 * LedgerCategoryController
 *
 * Handles CRUD operations for Ledger Categories (Income, Expense, Salary, etc.)
 * Categories are always tied to the currently logged-in client.
 */
class LedgerCategoryController extends Controller
{
    /**
     * Display a paginated list of ledger categories for the logged-in client.
     */
    public function index()
    {
        $ledgerCategories = $this->getClientCategories();

        return view('client.ledger-category.index', compact('ledgerCategories'));
    }

    /**
     * Show the form for creating a new ledger category.
     */
    public function create()
    {
        return view('client.ledger-category.form');
    }

    /**
     * Store a newly created ledger category for the logged-in client.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        // Automatically assign client_id so clients only manage their own categories
        LedgerCategory::create($data + ['client_id' => auth('client')->id()]);

        return redirect()
            ->route('client.ledger-categories.index')
            ->with('success', 'Ledger category created successfully.');
    }

    /**
     * Show the form for editing a specific ledger category.
     */
    public function edit(LedgerCategory $ledgerCategory)
    {
        $this->authorizeOwner($ledgerCategory);

        // Reuse helper to fetch categories for the index view
        $ledgerCategories = $this->getClientCategories();

        return view('client.ledger-category.index', compact('ledgerCategories', 'ledgerCategory'));
    }

    /**
     * Update the given ledger category.
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
     * Delete the given ledger category.
     */
    public function destroy(LedgerCategory $ledgerCategory)
    {
        $this->authorizeOwner($ledgerCategory);

        $ledgerCategory->delete();

        return redirect()
            ->route('client.ledger-categories.index')
            ->with('success', 'Ledger category deleted successfully.');
    }

    /*--------------------------------
    | PRIVATE HELPERS
    --------------------------------*/

    /**
     * Validate request for creating/updating a ledger category.
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255', // Category name is required
            'description' => 'nullable|string',         // Optional description
        ]);
    }

    /**
     * Ensure the given category belongs to the logged-in client.
     * Prevents unauthorized access.
     */
    protected function authorizeOwner(LedgerCategory $category): void
    {
        if ($category->client_id !== auth('client')->id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Fetch paginated categories for the logged-in client.
     * Centralized so it's not repeated in multiple methods.
     */
    protected function getClientCategories()
    {
        return LedgerCategory::where('client_id', auth('client')->id())
            ->latest()
            ->paginate(10);
    }
}
