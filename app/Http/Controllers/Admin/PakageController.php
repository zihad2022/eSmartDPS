<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PakageRequest;
use App\Models\Pakage;
use Illuminate\Http\Request;

class PakageController extends Controller
{
    public function index(Request $request)
    {
        $pakages = Pakage::query()
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.pakage.index', compact('pakages'));
    }

    public function create()
    {
        return view('admin.pakage.form', ['pakage' => null]);
    }

    public function store(PakageRequest $request)
    {
        Pakage::create($request->validated());

        return redirect()
            ->route('admin.pakages.index')
            ->with('success', 'Pakage has been created successfully.');
    }

    public function show(Pakage $pakage)
    {
        return view('admin.pakage.show', compact('pakage'));
    }

    public function edit(Pakage $pakage)
    {
        return view('admin.pakage.form', compact('pakage'));
    }

    public function update(PakageRequest $request, Pakage $pakage)
    {
        $pakage->update($request->validated());

        return redirect()
            ->route('admin.pakages.index')
            ->with('success', 'Pakage has been updated successfully.');
    }

    public function destroy(Pakage $pakage)
    {
        $pakage->delete();

        return redirect()
            ->route('admin.pakages.index')
            ->with('success', 'Pakage has been deleted.');
    }
}
