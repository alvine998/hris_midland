<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBulkLeaveRequestRequest;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Section;
use App\Models\WorkLocation;
use App\Services\LeaveInclusiveDayService;
use App\Services\ListSearchService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['on_progress', 'approved', 'rejected', 'cancelled'])],
            'leave_type_id' => ['nullable', 'integer', 'exists:leave_types,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'work_location_id' => ['nullable', 'integer', 'exists:work_locations,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $companies = Company::orderBy('name')->get();

        $leaveRequests = LeaveRequest::with(['employee.company', 'employee.department', 'employee.division', 'employee.section', 'employee.workLocation', 'leaveType', 'delegatedEmployee'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = ListSearchService::searchTerm($request);

                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('nip', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->when(filled($filters['leave_type_id'] ?? null), fn ($query) => $query->where('leave_type_id', $filters['leave_type_id']))
            ->when(filled($filters['date_from'] ?? null), fn ($query) => $query->whereDate('start_date', '>=', $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn ($query) => $query->whereDate('end_date', '<=', $filters['date_to']))
            ->when(filled($filters['company_id'] ?? null), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('company_id', $filters['company_id'])))
            ->when(filled($filters['department_id'] ?? null), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('department_id', $filters['department_id'])))
            ->when(filled($filters['division_id'] ?? null), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('division_id', $filters['division_id'])))
            ->when(filled($filters['section_id'] ?? null), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('section_id', $filters['section_id'])))
            ->when(filled($filters['work_location_id'] ?? null), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('work_location_id', $filters['work_location_id'])))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('leave-requests.index', [
            'leaveRequests' => $leaveRequests,
            'leaveTypes' => LeaveType::orderBy('name')->get(),
            'companies' => $companies,
            'departments' => Department::orderBy('name')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'sections' => Section::orderBy('name')->get(),
            'workLocations' => WorkLocation::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('leave-requests.create', [
            'leaveTypes' => LeaveType::orderBy('name')->get(),
            'employees' => Employee::orderBy('name')->get(),
        ]);
    }

    public function store(StoreLeaveRequestRequest $request, LeaveInclusiveDayService $service): RedirectResponse
    {
        $data = $request->validated();
        $data['inclusive_days'] = $service->calculate(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
        );

        $leaveRequest = LeaveRequest::create($data);
        $this->logCreated($leaveRequest, 'Leave Management');

        return redirect()->route('leave-requests.index')->with('success', 'Leave request created successfully.');
    }

    public function bulkStore(StoreBulkLeaveRequestRequest $request, LeaveInclusiveDayService $service): RedirectResponse
    {
        $data = $request->validated();
        $employeeIds = $data['employee_ids'];
        unset($data['employee_ids']);
        $data['inclusive_days'] = $service->calculate(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
        );

        foreach ($employeeIds as $employeeId) {
            $leaveRequest = LeaveRequest::create([
                ...$data,
                'employee_id' => $employeeId,
            ]);
            $this->logCreated($leaveRequest, 'Leave Management');
        }

        return redirect()->route('leave-requests.index')->with('success', count($employeeIds).' leave request(s) created successfully.');
    }

    public function edit(LeaveRequest $leaveRequest): View
    {
        return view('leave-requests.edit', [
            'leaveRequest' => $leaveRequest,
            'leaveTypes' => LeaveType::orderBy('name')->get(),
            'employees' => Employee::orderBy('name')->get(),
        ]);
    }

    public function update(StoreLeaveRequestRequest $request, LeaveRequest $leaveRequest, LeaveInclusiveDayService $service): RedirectResponse
    {
        $data = $request->validated();
        $data['inclusive_days'] = $service->calculate(
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
        );

        $oldData = $leaveRequest->attributesToArray();
        $leaveRequest->update($data);
        $this->logUpdated($leaveRequest, $oldData, 'Leave Management');

        return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully.');
    }

    public function destroy(LeaveRequest $leaveRequest): RedirectResponse
    {
        $oldData = $leaveRequest->attributesToArray();
        $leaveRequest->delete();
        $this->logDeleted($leaveRequest, $oldData, 'Leave Management');

        return back()->with('success', 'Leave request deleted successfully.');
    }
}
