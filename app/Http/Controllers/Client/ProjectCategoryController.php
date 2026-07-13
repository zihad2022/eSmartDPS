<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectCategoryController extends Controller
{
    /**
     * Display a paginated list of project categories.
     */
    public function index(Request $request)
    {
        $clientId = owner_client_id();
        $search = $request->get('search'); // Capture search query

        // -----------------------------
        // 1. Check if editing a category
        // -----------------------------
        if ($request->has('edit')) {
            $editCategory = ProjectCategory::where('client_id', $clientId)
                ->findOrFail($request->edit);

            $categories = ProjectCategory::where('client_id', $clientId)
                ->when($search, function ($q, $search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
                })
                ->select('id', 'name', 'slug', 'created_at')
                ->latest()
                ->paginate(10)
                ->appends($request->query());

            return view('client.project-category.index', compact('editCategory', 'categories', 'search'));
        }

        // -----------------------------
        // 2. Default category list with search
        // -----------------------------
        $categories = ProjectCategory::where('client_id', $clientId)
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'slug', 'created_at')
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('client.project-category.index', compact('categories', 'search'));
    }


    /**
     * Show the form to create a new category.
     */
    public function create()
    {
        // -----------------------------
        // 1. Return empty form
        // -----------------------------
        return view('client.project-category.form', ['category' => null]);
    }

    /**
     * Store a new project category in the database.
     */
    public function store(Request $request)
    {
        $clientId = owner_client_id();

        // -----------------------------
        // 1. Validate category name unique per client
        // -----------------------------
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_categories')->where(fn($q) => $q->where('client_id', $clientId)),
            ],
        ]);

        // -----------------------------
        // 2. Create category
        // -----------------------------
        ProjectCategory::create([
            'client_id' => $clientId,
            'name' => $validated['name'],
        ]);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form to edit an existing category.
     */
    public function edit(ProjectCategory $projectCategory)
    {
        $this->authorizeCategory($projectCategory);

        // -----------------------------
        // 1. Return edit form
        // -----------------------------
        return view('client.project-category.form', ['category' => $projectCategory]);
    }

    /**
     * Update an existing project category.
     */
    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $this->authorizeCategory($projectCategory);

        $clientId = owner_client_id();

        // -----------------------------
        // 1. Validate uniqueness per client
        // -----------------------------
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_categories')
                    ->ignore($projectCategory->id)
                    ->where(fn($q) => $q->where('client_id', $clientId)),
            ],
        ]);

        // -----------------------------
        // 2. Update category
        // -----------------------------
        $projectCategory->update(['name' => $validated['name']]);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete a project category.
     */
    public function destroy(ProjectCategory $projectCategory)
    {
        $this->authorizeCategory($projectCategory);

        if ($projectCategory->projects()->exists()) {
            return back()->with('error', 'This category is used by projects and cannot be deleted.');
        }

        $projectCategory->delete();

        // -----------------------------
        // 2. Redirect with success
        // -----------------------------
        return redirect()->route('client.project-categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Helper: Authorize category belongs to current client.
     */
    protected function authorizeCategory(ProjectCategory $category)
    {
        abort_unless($category->client_id === owner_client_id(), 403);
    }
}
