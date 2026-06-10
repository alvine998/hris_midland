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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $companies = Company::orderBy('name')->get();

        $leaveRequests = LeaveRequest::with(['employee.company', 'employee.department', 'employee.division', 'employee.section', 'employee.workLocation', 'leaveType', 'delegatedEmployee'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->query('search'));

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
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('leave_type_id'), fn ($query) => $query->where('leave_type_id', $request->query('leave_type_id')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('start_date', '>=', $request->query('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('end_date', '<=', $request->query('date_to')))
            ->when($request->filled('company_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('company_id', $request->query('company_id'))))
            ->when($request->filled('department_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('department_id', $request->query('department_id'))))
            ->when($request->filled('division_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('division_id', $request->query('division_id'))))
            ->when($request->filled('section_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('section_id', $request->query('section_id'))))
            ->when($request->filled('work_location_id'), fn ($query) => $query->whereHas('employee', fn ($query) => $query->where('work_location_id', $request->query('work_location_id'))))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('leave-requests.index', [
            'leaveRequests' => $leaveRequests,
            'employees' => Employee::orderBy('name')->get(),
            'leaveTypes' => LeaveType::orderBy('name')->get(),
            'companies' => $companies,
            'departments' => Department::orderBy('name')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'sections' => Section::orderBy('name')->get(),
            'workLocations' => WorkLocation::orderBy('name')->get(),
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

        return back()->with('success', 'Leave request created successfully.');
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

        return back()->with('success', count($employeeIds).' leave request(s) created successfully.');
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

        return back()->with('success', 'Leave request updated successfully.');
    }

    public function destroy(LeaveRequest $leaveRequest): RedirectResponse
    {
        $oldData = $leaveRequest->attributesToArray();
        $leaveRequest->delete();
        $this->logDeleted($leaveRequest, $oldData, 'Leave Management');

        return back()->with('success', 'Leave request deleted successfully.');
    }
}
