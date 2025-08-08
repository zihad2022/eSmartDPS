<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PakageRequest;
use App\Models\Pakage;
use Illuminate\Http\Request;

class PakageController extends Controller
{
    /**
     * Display a paginated list of packages.
     * Allows filtering by active/inactive status using URL query (?status=active/inactive)
     */
    public function index(Request $request)
    {
        $pakages = Pakage::query()
            // Filter only active packages if ?status=active
            ->when($request->status === 'active', fn ($q) => $q->active())
            // Filter only inactive packages if ?status=inactive
            ->when($request->status === 'inactive', fn ($q) => $q->inactive())
            ->orderBy('id', 'desc') // Sort by id in descending order
            ->paginate(10) // Paginate results (10 per page)
            ->appends($request->query()); // Keep query parameters in pagination links

        return view('admin.pakage.index', compact('pakages'));
    }

    /**
     * Show the form to create a new package.
     */
    public function create()
    {
        // Passing pakage as null (used in form blade to detect create vs edit)
        return view('admin.pakage.form', ['pakage' => null]);
    }

    /**
     * Store a newly created package in the database.
     * Uses PakageRequest for validation.
     */
    public function store(PakageRequest $request)
    {
        Pakage::create($request->validated()); // Save validated data

        return redirect()
            ->route('admin.pakages.index')
            ->with('success', 'Pakage has been created successfully.');
    }

    /**
     * Display details of a specific package.
     */
    public function show(Pakage $pakage)
    {
        return view('admin.pakage.show', compact('pakage'));
    }

    /**
     * Show the form to edit an existing package.
     */
    public function edit(Pakage $pakage)
    {
        return view('admin.pakage.form', compact('pakage'));
    }

    /**
     * Update an existing package with validated data.
     */
    public function update(PakageRequest $request, Pakage $pakage)
    {
        $pakage->update($request->validated());

        return redirect()
            ->route('admin.pakages.index')
            ->with('success', 'Pakage has been updated successfully.');
    }

    /**
     * Delete a specific package from the database.
     */
    public function destroy(Pakage $pakage)
    {
        $pakage->delete();

        return redirect()
            ->route('admin.pakages.index')
            ->with('success', 'Pakage has been deleted.');
    }
}
