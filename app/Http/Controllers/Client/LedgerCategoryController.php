<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        LedgerCategory::create($data + ['client_id' => owner_client_id()]);

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

        $ledgerCategory->update($this->validateRequest($request, $ledgerCategory));

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

        if ($ledgerCategory->ledgers()->exists()) {
            return back()->with('error', 'This category is used by ledger entries and cannot be deleted.');
        }

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
    protected function validateRequest(Request $request, ?LedgerCategory $category = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ledger_categories', 'name')
                    ->ignore($category?->id)
                    ->where(fn ($query) => $query->where('client_id', owner_client_id())),
            ],
            'description' => ['nullable', 'string'],
        ]);
    }

    /**
     * Ensure the given category belongs to the logged-in client.
     * Prevents unauthorized access.
     */
    protected function authorizeOwner(LedgerCategory $category): void
    {
        if ($category->client_id !== owner_client_id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Fetch paginated categories for the logged-in client.
     * Centralized so it's not repeated in multiple methods.
     */
    protected function getClientCategories()
    {
        return LedgerCategory::where('client_id', owner_client_id())
            ->latest()
            ->paginate(10);
    }
}
