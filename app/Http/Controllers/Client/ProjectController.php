<?php

namespace App\Http\Controllers\Client;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProjectRequest;
use App\Models\Client;
use App\Models\ClientSetting;
use App\Models\Project;
use App\Models\ProjectCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects with optional search & status filters.
     */
    public function index(Request $request)
    {
        // -----------------------------
        // 1. Get client ID
        // -----------------------------
        $clientId = owner_client_id();

        // -----------------------------
        // 2. Capture filters
        // -----------------------------
        $search = $request->get('search');
        $status = $request->query('status');

        // -----------------------------
        // 3. Build base query with eager loading
        // -----------------------------
        $projectsQuery = Project::with('projectCategory')
            ->where('client_id', $clientId)
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('projectCategory', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            })
            ->select([
                'id',
                'name',
                'project_category_id',
                'investment_amount',
                'expected_return',
                'start_date',
                'end_date',
                'duration',
                'status',
                'created_at',
            ])
            ->latest('id');

        // -----------------------------
        // 4. Apply status filter (if valid)
        // -----------------------------
        $statusMap = [
            'active'    => ProjectStatus::ACTIVE,
            'completed' => ProjectStatus::COMPLETED,
            'cancelled' => ProjectStatus::CANCELLED,
        ];

        if ($status && isset($statusMap[$status])) {
            $projectsQuery->where('status', $statusMap[$status]);
        }

        // -----------------------------
        // 5. Paginate results
        // -----------------------------
        $projects = $projectsQuery->paginate(10)->withQueryString();

        // -----------------------------
        // 6. Dashboard stats (counts)
        // -----------------------------
        $counts = Project::where('client_id', $clientId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled_count
            ', [
                ProjectStatus::ACTIVE->value,
                ProjectStatus::COMPLETED->value,
                ProjectStatus::CANCELLED->value,
            ])
            ->first();

        // -----------------------------
        // 7. Totals (only current page)
        // -----------------------------
        $totalInvestmentAmount = $projects->sum('investment_amount');
        $totalExpectedReturn   = $projects->sum('expected_return');

        // -----------------------------
        // 8. Client settings
        // -----------------------------
        $settings = ClientSetting::where('client_id', $clientId)->first();

        // -----------------------------
        // 9. Render view
        // -----------------------------
        return view('client.project.index', [
            'projects'              => $projects,
            'totalProjects'         => $counts->total,
            'activeProjects'        => $counts->active_count,
            'completedProjects'     => $counts->completed_count,
            'cancelledProjects'     => $counts->cancelled_count,
            'totalInvestmentAmount' => $totalInvestmentAmount,
            'totalExpectedReturn'   => $totalExpectedReturn,
            'settings'              => $settings,
            'search'                => $search,
        ]);
    }

    /**
     * Show form for creating a new project.
     */
    public function create()
    {
        // -----------------------------
        // 1. Get categories
        // -----------------------------
        $categories = $this->getClientCategories();

        // -----------------------------
        // 2. Render view
        // -----------------------------
        return view('client.project.form', [
            'categories' => $categories,
            'project'    => null,
        ]);
    }

    /**
     * Store a new project.
     */
    public function store(ProjectRequest $request)
    {
        // -----------------------------
        // 1. Get client
        // -----------------------------
        $client = Client::findOrFail(owner_client_id());

        // -----------------------------
        // 2. Check package project limit
        // -----------------------------
        if (! $client->canAddProject()) {
            return back()->with('error', 'You have reached the maximum limit of projects for your package.');
        }

        // -----------------------------
        // 3. Validate & prepare data
        // -----------------------------
        $validated = $request->validated();
        $validated['duration']  = $this->calculateDuration($validated['start_date'], $validated['end_date']);
        $validated['client_id'] = owner_client_id();

        // -----------------------------
        // 4. Create project
        // -----------------------------
        Project::create($validated);

        return redirect()->route('client.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display a project.
     */
    public function show(Project $project)
    {
        // -----------------------------
        // 1. Authorize project
        // -----------------------------
        $this->authorizeProject($project);

        // -----------------------------
        // 2. Return project view
        // -----------------------------
        return view('client.project.show', compact('project'));
    }

    /**
     * Show edit form.
     */
    public function edit(Project $project)
    {
        // -----------------------------
        // 1. Authorize project
        // -----------------------------
        $this->authorizeProject($project);

        // -----------------------------
        // 2. Return project form view
        // -----------------------------
        return view('client.project.form', [
            'project'    => $project,
            'categories' => $this->getClientCategories(),
        ]);
    }

    /**
     * Update a project.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        // -----------------------------
        // 1. Authorize project
        // -----------------------------
        $this->authorizeProject($project);

        // -----------------------------
        // 2. Validate & prepare data
        // -----------------------------
        $validated = $request->validated();
        $validated['duration'] = $this->calculateDuration($validated['start_date'], $validated['end_date']);

        // -----------------------------
        // 3. Update project
        // -----------------------------
        $project->update($validated);

        // -----------------------------
        // 4. Redirect to projects index
        // -----------------------------
        return redirect()->route('client.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Delete a project.
     */
    public function destroy(Project $project)
    {
        // -----------------------------
        // 1. Authorize project
        // -----------------------------
        $this->authorizeProject($project);

        // -----------------------------
        // 2. Delete project
        // -----------------------------
        $project->delete();

        // -----------------------------
        // 3. Redirect to projects index
        // -----------------------------
        return redirect()->route('client.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Ensure project belongs to logged-in client.
     */
    protected function authorizeProject(Project $project): void
    {
        abort_if($project->client_id !== owner_client_id(), 403, 'Unauthorized');
    }

    /**
     * Get categories of logged-in client.
     */
    protected function getClientCategories()
    {
        // -----------------------------
        // 1. Get categories
        // -----------------------------
        return ProjectCategory::where('client_id', owner_client_id())
            ->select('id', 'name')
            ->get();
    }

    /**
     * Calculate duration between two dates (y, m, d).
     */
    protected function calculateDuration(?string $start, ?string $end): ?string
    {
        // --------------------------------------------
        // 1. Return null if start or end date is missing
        // --------------------------------------------
        if (! $start || ! $end) {
            return null;
        }
    
        // --------------------------------------------
        // 2. Get difference between the two dates
        // --------------------------------------------
        $diff = Carbon::parse($start)->diff(Carbon::parse($end));
    
        // --------------------------------------------
        // 3. Build formatted duration string
        // --------------------------------------------
        $duration = trim(
            ($diff->y ? "{$diff->y} years " : '') .
            ($diff->m ? "{$diff->m} months " : '') .
            ($diff->d ? "{$diff->d} days" : '')
        );
    
        // --------------------------------------------
        // 4. Return result (default to "0 days" if empty)
        // --------------------------------------------
        return $duration ?: '0 days';
    }
}
