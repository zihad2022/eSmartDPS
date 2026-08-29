<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectCategoryController extends Controller
{
    /**
     * Display a paginated list of project categories.
     */
    public function index(Request $request): View
    {
        $clientId = owner_client_id();
        $search = $request->get('search');

        $categories = ProjectCategory::where('client_id', $clientId)
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'slug', 'created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $editCategory = $request->filled('edit')
            ? ProjectCategory::where('client_id', $clientId)->find($request->edit)
            : null;

        return view('client.project-category.index', compact('editCategory', 'categories', 'search'));
    }

    /**
     * Show the form to create a new category.
     */
    public function create(): View
    {
        return view('client.project-category.form', ['category' => null]);
    }

    /**
     * Store a new project category in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $clientId = owner_client_id();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
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

    /**
     * Show the form to edit an existing category.
     */
    public function edit(ProjectCategory $projectCategory): View
    {
        $this->authorizeCategory($projectCategory);

        return view('client.project-category.form', ['category' => $projectCategory]);
    }

    /**
     * Update an existing project category.
     */
    public function update(Request $request, ProjectCategory $projectCategory): RedirectResponse
    {
        $this->authorizeCategory($projectCategory);

        $clientId = owner_client_id();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_categories')
                    ->ignore($projectCategory->id)
                    ->where(fn ($q) => $q->where('client_id', $clientId)),
            ],
        ]);

        $projectCategory->update(['name' => $validated['name']]);

        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete a project category.
     */
    public function destroy(ProjectCategory $projectCategory): RedirectResponse
    {
        $this->authorizeCategory($projectCategory);

        if ($projectCategory->projects()->exists()) {
            return back()->with('error', 'This category is used by projects and cannot be deleted.');
        }

        $projectCategory->delete();

        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Helper: Authorize category belongs to current client.
     */
    protected function authorizeCategory(ProjectCategory $category): void
    {
        abort_unless($category->client_id === owner_client_id(), 403);
    }
}
