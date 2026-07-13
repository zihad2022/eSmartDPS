<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Packages\CreatePackageAction;
use App\Actions\Admin\Packages\DeletePackageAction;
use App\Actions\Admin\Packages\GetPackageDetailsAction;
use App\Actions\Admin\Packages\GetPackagesAction;
use App\Actions\Admin\Packages\UpdatePackageAction;
use App\Domain\Packages\Models\Package;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request, GetPackagesAction $action): View
    {
        return view('admin.package.index', $action->execute(
            search: $request->string('search')->trim()->toString() ?: null,
            status: $request->string('status')->toString() ?: null,
        ));
    }

    public function create(): View
    {
        return view('admin.package.form', ['package' => null]);
    }

    public function store(PackageRequest $request, CreatePackageAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package has been created successfully.');
    }

    public function show(Package $package, GetPackageDetailsAction $action): View
    {
        return view('admin.package.show', $action->execute($package));
    }

    public function edit(Package $package): View
    {
        return view('admin.package.form', compact('package'));
    }

    public function update(
        PackageRequest $request,
        Package $package,
        UpdatePackageAction $action
    ): RedirectResponse {
        $action->execute($package, $request->validated());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package has been updated successfully.');
    }

    public function destroy(Package $package, DeletePackageAction $action): RedirectResponse
    {
        try {
            $action->execute($package);
        } catch (ValidationException $exception) {
            return back()->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package has been deleted.');
    }
}
