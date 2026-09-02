<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStock;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectStockController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProjectStock::query()->with(['project']);

        $query = ListSearchService::apply($query, $request, ['unit_code', 'type', 'block', 'status'], [
            'project' => ['name'],
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->query('project_id'));
        }

        $stocks = $query->latest()->paginate(10)->withQueryString();

        return view('marketing-sales.project-stocks.index', [
            'stocks' => $stocks,
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'statuses' => ['available' => 'Available', 'booked' => 'Booked', 'sold' => 'Sold', 'reserved' => 'Reserved'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $stock = ProjectStock::create($data);
        $this->logCreated($stock, 'Project Stocks');

        return back()->with('success', 'Project stock created successfully.');
    }

    public function update(Request $request, ProjectStock $projectStock): RedirectResponse
    {
        $data = $this->validated($request, $projectStock);
        $oldData = $projectStock->attributesToArray();

        $projectStock->update($data);
        $this->logUpdated($projectStock, $oldData, 'Project Stocks');

        return back()->with('success', 'Project stock updated successfully.');
    }

    public function destroy(ProjectStock $projectStock): RedirectResponse
    {
        $oldData = $projectStock->attributesToArray();
        $projectStock->delete();
        $this->logDeleted($projectStock, $oldData, 'Project Stocks');

        return back()->with('success', 'Project stock deleted successfully.');
    }

    private function validated(Request $request, ?ProjectStock $stock = null): array
    {
        $projectId = $request->input('project_id');

        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'unit_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('project_stocks', 'unit_code')
                    ->where(fn ($query) => $query->where('project_id', $projectId))
                    ->ignore($stock?->id),
            ],
            'type' => ['nullable', 'string', 'max:100'],
            'block' => ['nullable', 'string', 'max:50'],
            'land_size' => ['nullable', 'numeric', 'min:0'],
            'building_size' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,booked,sold,reserved'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
