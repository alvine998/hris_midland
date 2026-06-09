<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApprovalWorkflowRequest;
use App\Models\ApprovalWorkflow;
use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Section;
use App\Models\WorkLocation;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalWorkflowController extends Controller
{
    public function index(Request $request): View
    {
        $approvalWorkflows = ListSearchService::apply(ApprovalWorkflow::with(['company', 'department']), $request, ['name', 'description'], [
            'company' => ['name'],
            'department' => ['name'],
        ])->paginate(10)->withQueryString();

        return view('master-data.approval-workflows', [
            'approvalWorkflows' => $approvalWorkflows,
            'companies' => Company::all(),
            'departments' => Department::all(),
            'divisions' => Division::all(),
            'sections' => Section::all(),
            'workLocations' => WorkLocation::all(),
            'employees' => Employee::all(),
        ]);
    }

    public function store(StoreApprovalWorkflowRequest $request): RedirectResponse
    {
        ApprovalWorkflow::create($request->validated());

        return back()->with('success', 'Approval workflow created successfully.');
    }

    public function update(StoreApprovalWorkflowRequest $request, ApprovalWorkflow $approvalWorkflow): RedirectResponse
    {
        $approvalWorkflow->update($request->validated());

        return back()->with('success', 'Approval workflow updated successfully.');
    }

    public function destroy(ApprovalWorkflow $approvalWorkflow): RedirectResponse
    {
        $approvalWorkflow->delete();

        return back()->with('success', 'Approval workflow deleted successfully.');
    }
}
