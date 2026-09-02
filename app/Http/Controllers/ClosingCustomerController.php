<?php

namespace App\Http\Controllers;

use App\Models\ClosingCustomer;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectStock;
use App\Models\SurveyCustomer;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClosingCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = ClosingCustomer::query()->with(['lead', 'surveyCustomer', 'project', 'projectStock', 'salesPerson']);

        $query = ListSearchService::apply($query, $request, ['customer_name', 'customer_phone', 'status', 'payment_method'], [
            'project' => ['name'],
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->query('project_id'));
        }

        $closings = $query->latest()->paginate(10)->withQueryString();

        return view('marketing-sales.closing-customers.index', [
            'closings' => $closings,
            'leads' => Lead::orderBy('name')->get(['id', 'name', 'phone', 'email', 'project_id']),
            'surveyCustomers' => SurveyCustomer::orderBy('customer_name')->get(['id', 'customer_name']),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
            'projectStocks' => ProjectStock::orderBy('unit_code')->get(['id', 'unit_code', 'project_id']),
            'employees' => Employee::orderBy('name')->get(['id', 'name']),
            'statuses' => ['pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'],
            'paymentMethods' => ['cash' => 'Cash', 'kpr' => 'KPR', 'installment' => 'Installment', 'other' => 'Other'],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $closing = ClosingCustomer::create($data);
        $this->logCreated($closing, 'Closing Customers');

        return back()->with('success', 'Closing customer created successfully.');
    }

    public function update(Request $request, ClosingCustomer $closingCustomer): RedirectResponse
    {
        $data = $this->validated($request);
        $oldData = $closingCustomer->attributesToArray();

        $closingCustomer->update($data);
        $this->logUpdated($closingCustomer, $oldData, 'Closing Customers');

        return back()->with('success', 'Closing customer updated successfully.');
    }

    public function destroy(ClosingCustomer $closingCustomer): RedirectResponse
    {
        $oldData = $closingCustomer->attributesToArray();
        $closingCustomer->delete();
        $this->logDeleted($closingCustomer, $oldData, 'Closing Customers');

        return back()->with('success', 'Closing customer deleted successfully.');
    }

    private function validated(Request $request): array
    {
        $isFromLead = $request->boolean('is_from_lead');

        $data = $request->validate([
            'is_from_lead' => ['nullable', 'boolean'],
            'lead_id' => [$isFromLead ? 'required' : 'nullable', 'exists:leads,id'],
            'survey_customer_id' => ['nullable', 'exists:survey_customers,id'],
            'customer_name' => [$isFromLead ? 'nullable' : 'required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'project_id' => ['required', 'exists:projects,id'],
            'project_stock_id' => ['nullable', 'exists:project_stocks,id'],
            'closing_date' => ['nullable', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,kpr,installment,other'],
            'status' => ['required', 'in:pending,completed,cancelled'],
            'sales_person_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        unset($data['is_from_lead']);

        if ($isFromLead && ! empty($data['lead_id'])) {
            $lead = Lead::find($data['lead_id']);
            if ($lead) {
                $data['customer_name'] = ($data['customer_name'] ?? null) ?: $lead->name;
                $data['customer_phone'] = ($data['customer_phone'] ?? null) ?: $lead->phone;
                $data['customer_email'] = ($data['customer_email'] ?? null) ?: $lead->email;
            }
        } else {
            $data['lead_id'] = null;
        }

        return $data;
    }
}
