<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectCategoryController extends Controller
{
    public function index(Request $request)
    {
        $clientId = owner_client_id();

        // If editing a category
        if ($request->has('edit')) {
            $editCategory = ProjectCategory::where('client_id', $clientId)
                ->findOrFail($request->edit);

            $categories = ProjectCategory::select('id', 'name', 'slug', 'created_at')
                ->where('client_id', $clientId)
                ->latest()
                ->paginate(10);

            return view('client.project-category.index', compact('editCategory', 'categories'));
        }

        // Default category list
        $categories = ProjectCategory::select('id', 'name', 'slug', 'created_at')
            ->where('client_id', $clientId)
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
        $clientId = owner_client_id();

        // Validate category name unique per client
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('project_categories')->where(fn ($q) => $q->where('client_id', $clientId)),
            ],
        ]);

        ProjectCategory::create([
            'client_id' => $clientId,
            'name' => $validated['name'],
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

        $clientId = auth('client')->id();

        // Validate uniqueness only inside the same client
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('project_categories')->ignore($projectCategory->id)->where(fn ($q) => $q->where('client_id', $clientId)),
            ],
        ]);

        $projectCategory->update([
            'name' => $validated['name'],
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
