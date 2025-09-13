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
     * Display a paginated list of projects with optional status filter.
     */
    public function index(Request $request)
    {
        $clientId = auth('client')->id();
    
        // -----------------------------
        // 1. Capture search query
        // -----------------------------
        $search = $request->get('search');
    
        // -----------------------------
        // 2. Start query with eager loading for category relation
        // -----------------------------
        $projectsQuery = Project::with('projectCategory')
            ->where('client_id', $clientId)
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhereHas('projectCategory', function ($q2) use ($search) {
                              $q2->where('name', 'like', "%{$search}%");
                          });
                });
            })
            ->select(
                'id',
                'name',
                'project_category_id',
                'investment_amount',
                'expected_return',
                'start_date',
                'end_date',
                'duration',
                'status',
                'created_at'
            )
            ->latest('id');
    
        // -----------------------------
        // 3. Apply status filter if requested
        // -----------------------------
        $statusMap = [
            'active'    => ProjectStatus::ACTIVE,
            'completed' => ProjectStatus::COMPLETED,
            'cancelled' => ProjectStatus::CANCELLED,
        ];
    
        if ($status = $request->query('status')) {
            if (isset($statusMap[$status])) {
                $projectsQuery->where('status', $statusMap[$status]);
            }
        }
    
        // -----------------------------
        // 4. Paginate results
        // -----------------------------
        $projects = $projectsQuery->paginate(10)->withQueryString();
    
        // -----------------------------
        // 5. Fetch counts for dashboard stats
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
        // 6. Calculate totals for current page
        // -----------------------------
        $totalInvestmentAmount = $projects->sum('investment_amount');
        $totalExpectedReturn   = $projects->sum('expected_return');
    
        // -----------------------------
        // 7. Fetch client settings
        // -----------------------------
        $settings = ClientSetting::where('client_id', $clientId)->first();
    
        // -----------------------------
        // 8. Return view
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
            'search'                => $search, // keep search input populated
        ]);
    }
    

    /**
     * Show the form to create a new project.
     */
    public function create()
    {
        // -----------------------------
        // 1. Fetch categories for logged-in client
        // -----------------------------
        $categories = $this->getClientCategories();

        // -----------------------------
        // 2. Return view
        // -----------------------------
        return view('client.project.form', [
            'categories' => $categories,
            'project' => null,
        ]);
    }

    /**
     * Store a new project in the database.
     */
    public function store(ProjectRequest $request)
    {
        $client = Client::findOrFail(owner_client_id());

        // -----------------------------
        // 1. Check package project limit
        // -----------------------------
        if (! $client->canAddProject()) {
            return back()->with('error', 'You have reached the maximum limit of projects for your package.');
        }

        // -----------------------------
        // 2. Validate and prepare data
        // -----------------------------
        $validated = $request->validated();
        $validated['duration'] = $this->calculateDuration($validated['start_date'], $validated['end_date']);
        $validated['client_id'] = auth('client')->id();

        // -----------------------------
        // 3. Create project
        // -----------------------------
        Project::create($validated);

        // -----------------------------
        // 4. Redirect with success
        // -----------------------------
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
     * Show the form to edit an existing project.
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
     * Update an existing project in the database.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorizeProject($project);

        // -----------------------------
        // 1. Validate and prepare data
        // -----------------------------
        $validated = $request->validated();
        $validated['duration'] = $this->calculateDuration($validated['start_date'], $validated['end_date']);

        // -----------------------------
        // 2. Update project
        // -----------------------------
        $project->update($validated);

        // -----------------------------
        // 3. Redirect with success
        // -----------------------------
        return redirect()->route('client.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /** 
     * 🔹 Authorize that the project belongs to logged-in client 
     */
    protected function authorizeProject(Project $project): void
    {
        abort_if($project->client_id !== auth('client')->id(), 403, 'Unauthorized');
    }

    /** 
     * 🔹 Get only categories of logged-in client 
     */
    protected function getClientCategories()
    {
        return ProjectCategory::where('client_id', auth('client')->id())
            ->select('id', 'name')
            ->get();
    }

    /** 
     * 🔹 Calculate duration between two dates in years, months, days 
     */
    protected function calculateDuration(?string $start, ?string $end): ?string
    {
        if (! $start || ! $end) {
            return null;
        }

        $diff = Carbon::parse($start)->diff(Carbon::parse($end));

        return trim(
            ($diff->y ? "{$diff->y} years " : '') .
            ($diff->m ? "{$diff->m} months " : '') .
            ($diff->d ? "{$diff->d} days" : '')
        ) ?: '0 days';
    }
}
