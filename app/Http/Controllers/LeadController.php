<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Lead;
use App\Models\Project;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lead::query()->with(['project', 'assignedEmployee']);

        $query = ListSearchService::apply($query, $request, ['name', 'phone', 'email', 'status', 'source'], [
            'project' => ['name'],
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('source')) {
            $query->where('source', $request->query('source'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->query('project_id'));
        }

        $leads = $query->latest()->paginate(10)->withQueryString();

        return view('marketing-sales.leads.index', [
            'leads' => $leads,
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'employees' => Employee::orderBy('name')->get(['id', 'name']),
            'statuses' => ['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'survey_scheduled' => 'Survey Scheduled', 'converted' => 'Converted', 'lost' => 'Lost'],
            'sources' => ['website' => 'Website', 'referral' => 'Referral', 'ads' => 'Ads', 'walk_in' => 'Walk In', 'social_media' => 'Social Media', 'other' => 'Other'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by_user_id'] = $request->user()->id;

        $lead = Lead::create($data);
        $this->logCreated($lead, 'Leads');

        return back()->with('success', 'Lead created successfully.');
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $this->validated($request);
        $oldData = $lead->attributesToArray();

        $lead->update($data);
        $this->logUpdated($lead, $oldData, 'Leads');

        return back()->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $oldData = $lead->attributesToArray();
        $lead->delete();
        $this->logDeleted($lead, $oldData, 'Leads');

        return back()->with('success', 'Lead deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'source' => ['nullable', 'string', 'max:50'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'assigned_to' => ['nullable', 'exists:employees,id'],
            'status' => ['required', 'in:new,contacted,qualified,survey_scheduled,converted,lost'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'follow_up_date' => ['nullable', 'date'],
        ]);
    }
}
