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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $companies = Company::orderBy('name')->get();

        $employees = Employee::with(['company', 'department', 'division', 'section', 'jobPosition', 'religion', 'workLocation'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = trim((string) $request->query('search'));

                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->when($request->filled('company_id'), fn ($query) => $query->where('company_id', $request->query('company_id')))
            ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->query('department_id')))
            ->when($request->filled('division_id'), fn ($query) => $query->where('division_id', $request->query('division_id')))
            ->when($request->filled('section_id'), fn ($query) => $query->where('section_id', $request->query('section_id')))
            ->when($request->filled('work_location_id'), fn ($query) => $query->where('work_location_id', $request->query('work_location_id')))
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
            'employees' => Employee::whereKeyNot($employee->id)->get(),
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
