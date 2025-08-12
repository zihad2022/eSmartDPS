<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a paginated list of packages.
     * Allows filtering by active/inactive status using URL query (?status=active/inactive)
     */
    public function index(Request $request)
    {
        $packages = Package::query()
            // Filter only active packages if ?status=active
            ->when($request->status === 'active', fn ($q) => $q->active())
            // Filter only inactive packages if ?status=inactive
            ->when($request->status === 'inactive', fn ($q) => $q->inactive())
            ->orderBy('id', 'desc') // Sort by id in descending order
            ->paginate(10) // Paginate results (10 per page)
            ->appends($request->query()); // Keep query parameters in pagination links

        return view('admin.package.index', compact('packages'));
    }

    /**
     * Show the form to create a new package.
     */
    public function create()
    {
        // Passing package as null (used in form blade to detect create vs edit)
        return view('admin.package.form', ['package' => null]);
    }

    /**
     * Store a newly created package in the database.
     * Uses PackageRequest for validation.
     */
    public function store(PackageRequest $request)
    {
        Package::create($request->validated()); // Save validated data

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package has been created successfully.');
    }

    /**
     * Display details of a specific package.
     */
    public function show(Package $package)
    {
        return view('admin.package.show', compact('package'));
    }

    /**
     * Show the form to edit an existing package.
     */
    public function edit(Package $package)
    {
        return view('admin.package.form', compact('package'));
    }

    /**
     * Update an existing package with validated data.
     */
    public function update(PackageRequest $request, Package $package)
    {
        $package->update($request->validated());

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package has been updated successfully.');
    }

    /**
     * Delete a specific package from the database.
     */
    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package has been deleted.');
    }
}
