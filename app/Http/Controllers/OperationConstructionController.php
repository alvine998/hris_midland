<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectStock;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperationConstructionController extends Controller
{
    public function overview(): View
    {
        $metrics = [
            'totalProjects' => Project::count(),
            'ongoingProjects' => Project::where('status', 'ongoing')->count(),
            'planningProjects' => Project::where('status', 'planning')->count(),
            'completedProjects' => Project::where('status', 'completed')->count(),
            'totalStocks' => ProjectStock::count(),
            'availableStocks' => ProjectStock::where('status', 'available')->count(),
            'soldStocks' => ProjectStock::where('status', 'sold')->count(),
            'avgProgress' => (int) Project::avg('progress'),
        ];

        $projects = Project::with(['company', 'manager'])
            ->latest()
            ->limit(5)
            ->get();

        $statusBreakdown = Project::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $progressDistribution = [
            'labels' => ['0-25%', '26-50%', '51-75%', '76-100%'],
            'values' => [
                Project::whereBetween('progress', [0, 25])->count(),
                Project::whereBetween('progress', [26, 50])->count(),
                Project::whereBetween('progress', [51, 75])->count(),
                Project::whereBetween('progress', [76, 100])->count(),
            ],
        ];

        return view('operation-construction.overview', compact('metrics', 'projects', 'statusBreakdown', 'progressDistribution'));
    }

    public function projects(Request $request): View
    {
        $query = Project::query()->with(['company', 'manager']);

        $query = ListSearchService::apply($query, $request, ['name', 'code', 'location', 'status'], [
            'company' => ['name'],
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        return view('operation-construction.projects.index', [
            'projects' => $projects,
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'employees' => Employee::orderBy('name')->get(['id', 'name']),
            'statuses' => ['planning' => 'Planning', 'ongoing' => 'Ongoing', 'completed' => 'Completed', 'on_hold' => 'On Hold', 'cancelled' => 'Cancelled'],
        ]);
    }

    public function projectShow(Project $project): View
    {
        $project->load(['company', 'manager', 'stocks', 'leads', 'closingCustomers']);

        $stockStats = [
            'total' => $project->stocks()->count(),
            'available' => $project->stocks()->where('status', 'available')->count(),
            'booked' => $project->stocks()->where('status', 'booked')->count(),
            'sold' => $project->stocks()->where('status', 'sold')->count(),
        ];

        return view('operation-construction.projects.show', compact('project', 'stockStats'));
    }

    public function storeProject(Request $request): RedirectResponse
    {
        $data = $this->validateProject($request);

        $project = Project::create($data);
        $this->logCreated($project, 'Projects');

        return back()->with('success', 'Project created successfully.');
    }

    public function updateProject(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validateProject($request, $project->id);
        $oldData = $project->attributesToArray();

        $project->update($data);
        $this->logUpdated($project, $oldData, 'Projects');

        return back()->with('success', 'Project updated successfully.');
    }

    public function destroyProject(Project $project): RedirectResponse
    {
        $oldData = $project->attributesToArray();
        $project->delete();
        $this->logDeleted($project, $oldData, 'Projects');

        return back()->with('success', 'Project deleted successfully.');
    }

    private function validateProject(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:projects,code'.($ignoreId ? ",{$ignoreId}" : '')],
            'company_id' => ['nullable', 'exists:companies,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:planning,ongoing,completed,on_hold,cancelled'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['required', 'integer', 'between:0,100'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
