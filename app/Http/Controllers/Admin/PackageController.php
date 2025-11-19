<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Package\BillingCycle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Domain\Packages\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a paginated list of packages.
     * Supports filtering by search, active/inactive status, and billing cycle.
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1. Build query with filters
        // -----------------------------
        $packages = Package::query()
            // Search by name or description
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            // Filter active packages (?status=active)
            ->when($request->status === 'active', fn ($q) => $q->active())
            // Filter inactive packages (?status=inactive)
            ->when($request->status === 'inactive', fn ($q) => $q->inactive())
            ->latest('id') // Sort by newest first
            ->paginate(10) // Paginate results
            ->appends($request->query()); // Preserve query params

        // -----------------------------
        // 2. Calculate package stats
        // -----------------------------
        $activePackages   = Package::active()->count();
        $inactivePackages = Package::inactive()->count();
        $monthlyPackages  = Package::where('billing_cycle', BillingCycle::MONTHLY)->count();
        $yearlyPackages   = Package::where('billing_cycle', BillingCycle::YEARLY)->count();

        // -----------------------------
        // 3. Return package list view
        // -----------------------------
        return view('admin.package.index', compact(
            'packages',
            'activePackages',
            'inactivePackages',
            'monthlyPackages',
            'yearlyPackages'
        ));
    }

    /**
     * Show the form to create a new package.
     */
    public function create()
    {
        // -----------------------------
        // Pass null package to form
        // -----------------------------
        // In the Blade, this helps detect create vs edit mode.
        return view('admin.package.form', ['package' => null]);
    }

    /**
     * Store a newly created package in the database.
     */
    public function store(PackageRequest $request)
    {
        // -----------------------------
        // 1. Validate request
        // -----------------------------
        $data = $request->validated();

        // -----------------------------
        // 2. Save package
        // -----------------------------
        Package::create($data);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package has been created successfully.');
    }

    /**
     * Display details of a specific package.
     */
    public function show(Package $package)
    {
        // -----------------------------
        // Show single package details
        // -----------------------------
        return view('admin.package.show', compact('package'));
    }

    /**
     * Show the form to edit an existing package.
     */
    public function edit(Package $package)
    {
        // -----------------------------
        // Pass existing package to form
        // -----------------------------
        return view('admin.package.form', compact('package'));
    }

    /**
     * Update an existing package.
     */
    public function update(PackageRequest $request, Package $package)
    {
        // -----------------------------
        // 1. Validate request
        // -----------------------------
        $data = $request->validated();

        // -----------------------------
        // 2. Update package
        // -----------------------------
        $package->update($data);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package has been updated successfully.');
    }

    /**
     * Delete a specific package.
     */
    public function destroy(Package $package)
    {
        // -----------------------------
        // 1. Delete package
        // -----------------------------
        $package->delete();

        // -----------------------------
        // 2. Redirect with success
        // -----------------------------
        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package has been deleted.');
    }
}
