<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectCategoryController extends Controller
{
    public function index()
    {
        $clientId = auth('client')->id();

        $categories = ProjectCategory::select('id', 'name', 'slug', 'created_at')
            ->where('client_id', owner_client_id())
            ->latest()
            ->paginate(10);

        return view('client.project-category.index', compact('categories'));
    }

    public function create()
    {
        return view('client.project-category.form', ['category' => null]);
    }

    public function store(Request $request)
    {
        $clientId = auth('client')->id();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:project_categories,name'],
        ]);

        ProjectCategory::create([
            'client_id' => owner_client_id(),
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(ProjectCategory $projectCategory)
    {
        $this->authorizeCategory($projectCategory);

        return view('client.project-category.form', ['category' => $projectCategory]);
    }

    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $this->authorizeCategory($projectCategory);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:project_categories,name,'.$projectCategory->id],
        ]);

        $projectCategory->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(ProjectCategory $projectCategory)
    {
        $this->authorizeCategory($projectCategory);

        $projectCategory->delete();

        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    protected function authorizeCategory(ProjectCategory $category)
    {
        abort_unless($category->client_id === auth('client')->id(), 403);
    }
}
