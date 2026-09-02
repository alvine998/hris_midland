<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectStock;
use App\Models\SurveyCustomer;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = SurveyCustomer::query()->with(['lead', 'project', 'projectStock', 'surveyor']);

        $query = ListSearchService::apply($query, $request, ['customer_name', 'customer_phone', 'status', 'interest_level'], [
            'project' => ['name'],
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('interest_level')) {
            $query->where('interest_level', $request->query('interest_level'));
        }

        $surveys = $query->latest()->paginate(10)->withQueryString();

        return view('marketing-sales.survey-customers.index', [
            'surveys' => $surveys,
            'leads' => Lead::orderBy('name')->get(['id', 'name', 'phone', 'email', 'project_id']),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'projectStocks' => ProjectStock::orderBy('unit_code')->get(['id', 'unit_code', 'project_id']),
            'employees' => Employee::orderBy('name')->get(['id', 'name']),
            'statuses' => ['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'],
            'interestLevels' => ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $survey = SurveyCustomer::create($data);
        $this->logCreated($survey, 'Survey Customers');

        return back()->with('success', 'Survey customer created successfully.');
    }

    public function update(Request $request, SurveyCustomer $surveyCustomer): RedirectResponse
    {
        $data = $this->validated($request);
        $oldData = $surveyCustomer->attributesToArray();

        $surveyCustomer->update($data);
        $this->logUpdated($surveyCustomer, $oldData, 'Survey Customers');

        return back()->with('success', 'Survey customer updated successfully.');
    }

    public function destroy(SurveyCustomer $surveyCustomer): RedirectResponse
    {
        $oldData = $surveyCustomer->attributesToArray();
        $surveyCustomer->delete();
        $this->logDeleted($surveyCustomer, $oldData, 'Survey Customers');

        return back()->with('success', 'Survey customer deleted successfully.');
    }

    private function validated(Request $request): array
    {
        $isFromLead = $request->boolean('is_from_lead');

        $data = $request->validate([
            'is_from_lead' => ['nullable', 'boolean'],
            'lead_id' => [$isFromLead ? 'required' : 'nullable', 'exists:leads,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'project_stock_id' => ['nullable', 'exists:project_stocks,id'],
            'customer_name' => [$isFromLead ? 'nullable' : 'required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'survey_date' => ['nullable', 'date'],
            'surveyor_id' => ['nullable', 'exists:employees,id'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'interest_level' => ['nullable', 'in:low,medium,high'],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'next_action' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,completed,cancelled'],
        ]);

        unset($data['is_from_lead']);

        if ($isFromLead && ! empty($data['lead_id'])) {
            $lead = Lead::find($data['lead_id']);
            if ($lead) {
                $data['customer_name'] = ($data['customer_name'] ?? null) ?: $lead->name;
                $data['customer_phone'] = ($data['customer_phone'] ?? null) ?: $lead->phone;
                $data['customer_email'] = ($data['customer_email'] ?? null) ?: $lead->email;
                $data['project_id'] = ($data['project_id'] ?? null) ?: $lead->project_id;
            }
        } else {
            $data['lead_id'] = null;
        }

        return $data;
    }
}
