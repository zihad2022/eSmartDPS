<?php

namespace App\Http\Controllers\Client;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProjectRequest;
use App\Models\Client;
use App\Models\ClientSetting;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a paginated list of projects with search and status filters.
     */
    public function index(Request $request)
    {
        $clientId = owner_client_id();
        $search = $request->get('search');
        $status = $request->query('status');

        $projectsQuery = Project::with('projectCategory')
            ->where('client_id', $clientId)
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhereHas('projectCategory', fn ($q2) => $q2->where('name', 'like', "%{$search}%")
                        );
                });
            })
            ->select([
                'id',
                'name',
                'project_category_id',
                'investment_amount',
                'expected_return',
                'expected_return_type',
                'start_date',
                'end_date',
                'status',
                'created_at',
            ])
            ->latest('id');

        $statusMap = [
            'active' => ProjectStatus::ACTIVE,
            'completed' => ProjectStatus::COMPLETED,
            'cancelled' => ProjectStatus::CANCELLED,
        ];

        if ($status && isset($statusMap[$status])) {
            $projectsQuery->where('status', $statusMap[$status]);
        }

        $projects = $projectsQuery->paginate(10)->withQueryString();

        // Summary stats
        $stats = Project::where('client_id', $clientId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled_count,
                SUM(investment_amount) as total_investment,
                SUM(expected_return) as total_return
            ', [
                ProjectStatus::ACTIVE->value,
                ProjectStatus::COMPLETED->value,
                ProjectStatus::CANCELLED->value,
            ])
            ->first();

        $pageInvestmentAmount = $projects->sum('investment_amount');
        $pageExpectedReturn = $projects->sum('expected_return');

        $settings = ClientSetting::where('client_id', $clientId)->first();

        return view('client.project.index', [
            'projects' => $projects,
            'totalProjects' => $stats->total ?? 0,
            'activeProjects' => $stats->active_count ?? 0,
            'completedProjects' => $stats->completed_count ?? 0,
            'cancelledProjects' => $stats->cancelled_count ?? 0,
            'totalInvestmentAmount' => $stats->total_investment ?? 0,
            'totalExpectedReturn' => $stats->total_return ?? 0,
            'pageInvestmentAmount' => $pageInvestmentAmount,
            'pageExpectedReturn' => $pageExpectedReturn,
            'settings' => $settings,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('client.project.form', [
            'categories' => $this->getClientCategories(),
            'project' => null,
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(ProjectRequest $request)
    {
        $client = Client::findOrFail(owner_client_id());

        if (! $client->canAddProject()) {
            return back()->with('error', 'You have reached your project limit.');
        }

        $validated = $request->validated();
        $validated['client_id'] = owner_client_id();

        Project::create($validated);

        return redirect()->route('client.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display a specific project.
     */
    public function show(Project $project)
    {
        $this->authorizeProject($project);

        return view('client.project.show', compact('project'));
    }

    /**
     * Show the form for editing a project.
     */
    public function edit(Project $project)
    {
        $this->authorizeProject($project);

        return view('client.project.form', [
            'project' => $project,
            'categories' => $this->getClientCategories(),
        ]);
    }

    /**
     * Update an existing project.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validated();

        $project->update($validated);

        return redirect()->route('client.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove a project.
     */
    public function destroy(Project $project)
    {
        $this->authorizeProject($project);
        $project->delete();

        return redirect()->route('client.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Ensure the project belongs to the current client.
     */
    protected function authorizeProject(Project $project): void
    {
        abort_if($project->client_id !== owner_client_id(), 403, 'Unauthorized');
    }

    /**
     * Fetch all categories for the logged-in client.
     */
    protected function getClientCategories()
    {
        return ProjectCategory::where('client_id', owner_client_id())
            ->select('id', 'name')
            ->get();
    }
}
