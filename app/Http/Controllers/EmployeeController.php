<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Company;
use App\Models\ContractType;
use App\Models\Department;
use App\Models\Division;
use App\Models\DocumentType;
use App\Models\EducationType;
use App\Models\Employee;
use App\Models\Facility;
use App\Models\FamilyType;
use App\Models\JobPosition;
use App\Models\LeaveType;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\Section;
use App\Models\Shift;
use App\Models\WorkLocation;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'section_id' => ['nullable', 'integer', 'exists:sections,id'],
            'work_location_id' => ['nullable', 'integer', 'exists:work_locations,id'],
        ]);
        $companies = Company::orderBy('name')->get();

        $employees = Employee::with(['company', 'department', 'division', 'section', 'jobPosition', 'religion', 'workLocation'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = ListSearchService::searchTerm($request);

                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->when(filled($filters['company_id'] ?? null), fn ($query) => $query->where('company_id', $filters['company_id']))
            ->when(filled($filters['department_id'] ?? null), fn ($query) => $query->where('department_id', $filters['department_id']))
            ->when(filled($filters['division_id'] ?? null), fn ($query) => $query->where('division_id', $filters['division_id']))
            ->when(filled($filters['section_id'] ?? null), fn ($query) => $query->where('section_id', $filters['section_id']))
            ->when(filled($filters['work_location_id'] ?? null), fn ($query) => $query->where('work_location_id', $filters['work_location_id']))
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'companies' => $companies,
            'departments' => Department::orderBy('name')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'sections' => Section::orderBy('name')->get(),
            'workLocations' => WorkLocation::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('employees.create', [
            'companies' => Company::all(),
            'departments' => Department::all(),
            'divisions' => Division::all(),
            'sections' => Section::all(),
            'jobPositions' => JobPosition::all(),
            'religions' => Religion::all(),
            'workLocations' => WorkLocation::all(),
            'facilities' => Facility::orderBy('name')->get(),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = Employee::create($request->validated());
        $this->logCreated($employee);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'company',
            'department',
            'division',
            'section',
            'jobPosition',
            'religion',
            'workLocation',
            'contracts.contractType',
            'educationHistories.educationType',
            'families.relationship',
            'families.familyType',
            'workHistories',
            'salary',
            'attendances',
            'leaveBalance',
            'emergencyContacts.relationship',
            'documents.documentType',
            'leaveRequests.leaveType',
            'leaveRequests.delegatedEmployee',
            'employeeShifts.shift',
        ]);

        return view('employees.show', [
            'employee' => $employee,
            'contractTypes' => ContractType::all(),
            'familyTypes' => FamilyType::all(),
            'educationTypes' => EducationType::all(),
            'relationships' => Relationship::all(),
            'documentTypes' => DocumentType::all(),
            'leaveTypes' => LeaveType::all(),
            'shifts' => Shift::all(),
        ]);
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', [
            'employee' => $employee,
            'companies' => Company::all(),
            'departments' => Department::all(),
            'divisions' => Division::all(),
            'sections' => Section::all(),
            'jobPositions' => JobPosition::all(),
            'religions' => Religion::all(),
            'workLocations' => WorkLocation::all(),
            'facilities' => Facility::orderBy('name')->get(),
        ]);
    }

    public function update(StoreEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $oldData = $employee->attributesToArray();
        $employee->update($request->validated());
        $this->logUpdated($employee, $oldData);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $oldData = $employee->attributesToArray();
        $employee->delete();
        $this->logDeleted($employee, $oldData);

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
