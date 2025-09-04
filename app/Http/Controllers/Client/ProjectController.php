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
    public function index(Request $request)
    {
        $clientId = auth('client')->id();
    
        // Start query with eager loading for category relation
        $projectsQuery = Project::with('category')
            ->where('client_id', $clientId)
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
    
        // Map status string (from query param) to Enum values
        $statusMap = [
            'active'    => ProjectStatus::ACTIVE,
            'completed' => ProjectStatus::COMPLETED,
            'cancelled' => ProjectStatus::CANCELLED,
        ];
    
        // Apply status filter if valid status is requested
        if ($status = $request->query('status')) {
            if (isset($statusMap[$status])) {
                $projectsQuery->where('status', $statusMap[$status]);
            }
        }
    
        // Paginate with query string preserved
        $projects = $projectsQuery->paginate(10)->withQueryString();
    
        // Preload counts for quick dashboard stats
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
    
        // Totals (only for current page data)
        $totalInvestmentAmount = $projects->sum('investment_amount');
        $totalExpectedReturn   = $projects->sum('expected_return');

        $settings = ClientSetting::where('client_id', $clientId)->first();
    
        return view('client.project.index', [
            'projects'              => $projects,
            'totalProjects'         => $counts->total,
            'activeProjects'        => $counts->active_count,
            'completedProjects'     => $counts->completed_count,
            'cancelledProjects'     => $counts->cancelled_count,
            'totalInvestmentAmount' => $totalInvestmentAmount,
            'totalExpectedReturn'   => $totalExpectedReturn,
            'settings'              => $settings,
        ]);
    }
    

    public function create()
    {
        return view('client.project.form', [
            'categories' => $this->getClientCategories(),
            'project' => null,
        ]);
    }

    public function store(ProjectRequest $request)
    {
        $client = Client::findOrFail(owner_client_id());
        if (! $client->canAddProject()) {
            return back()->with('error', 'You have reached the maximum limit of projects for your package.');
        }
        $validated = $request->validated();
        $validated['duration'] = $this->calculateDuration($validated['start_date'], $validated['end_date']);
        $validated['client_id'] = auth('client')->id();

        Project::create($validated);

        return redirect()->route('client.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $this->authorizeProject($project);

        return view('client.project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorizeProject($project);

        return view('client.project.form', [
            'project' => $project,
            'categories' => $this->getClientCategories(),
        ]);
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validated();
        $validated['duration'] = $this->calculateDuration($validated['start_date'], $validated['end_date']);

        $project->update($validated);

        return redirect()->route('client.projects.index')->with('success', 'Project updated successfully.');
    }

    /** 🔹 Authorize Project */
    protected function authorizeProject(Project $project): void
    {
        abort_if($project->client_id !== auth('client')->id(), 403, 'Unauthorized');
    }

    /** 🔹 Get only categories of logged-in client */
    protected function getClientCategories()
    {
        return ProjectCategory::where('client_id', auth('client')->id())
            ->select('id', 'name')
            ->get();
    }

    /** 🔹 Calculate duration between two dates */
    protected function calculateDuration(?string $start, ?string $end): ?string
    {
        if (! $start || ! $end) {
            return null;
        }

        $diff = Carbon::parse($start)->diff(Carbon::parse($end));

        return trim(
            ($diff->y ? "{$diff->y} years " : '').
            ($diff->m ? "{$diff->m} months " : '').
            ($diff->d ? "{$diff->d} days" : '')
        ) ?: '0 days';
    }
}
