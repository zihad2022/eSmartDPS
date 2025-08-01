<?php

namespace App\Http\Controllers\Client;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $clientId = auth('client')->id();

        $projectsQuery = Project::with('category')
            ->where('client_id', $clientId)
            ->select('id', 'name', 'project_category_id', 'investment_amount', 'expected_return', 'start_date', 'end_date', 'duration', 'status', 'created_at');

        // 🔹 Filter by status (if provided)
        if ($request->filled('status')) {
            $projectsQuery->where('status', $request->status);
        }

        $projects = $projectsQuery->latest()->paginate(10);

        // 🔹 Preload counts to reduce duplicate queries
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

        $totalInvestmentAmount = $projects->sum('investment_amount');

        return view('client.project.index', [
            'projects' => $projects,
            'totalProjects' => $counts->total,
            'activeProjects' => $counts->active_count,
            'completedProjects' => $counts->completed_count,
            'cancelledProjects' => $counts->cancelled_count,
            'totalInvestmentAmount' => $totalInvestmentAmount,
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
