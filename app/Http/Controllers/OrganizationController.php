<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\StoreWorkLocationRequest;
use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use App\Models\WorkLocation;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function companies(Request $request): View
    {
        $companies = ListSearchService::apply(Company::query(), $request, ['name', 'email', 'phone', 'address', 'status'])
            ->paginate(5)
            ->withQueryString();

        return view('master-data.companies', [
            'companies' => $companies,
            'companiesList' => Company::all(),
        ]);
    }

    public function storeCompany(StoreCompanyRequest $request): RedirectResponse
    {
        $company = Company::create($request->validated());
        $this->logCreated($company);

        return redirect()->route('master-data.companies')->with('success', 'Company created successfully.');
    }

    public function updateCompany(StoreCompanyRequest $request, Company $company): RedirectResponse
    {
        $oldData = $company->attributesToArray();
        $company->update($request->validated());
        $this->logUpdated($company, $oldData);

        return redirect()->route('master-data.companies')->with('success', 'Company updated successfully.');
    }

    public function destroyCompany(Company $company): RedirectResponse
    {
        $oldData = $company->attributesToArray();
        $company->delete();
        $this->logDeleted($company, $oldData);

        return redirect()->route('master-data.companies')->with('success', 'Company deleted successfully.');
    }

    public function workLocations(Request $request): View
    {
        $workLocations = ListSearchService::apply(WorkLocation::query(), $request, ['name', 'address', 'city', 'province', 'type'])
            ->paginate(5)
            ->withQueryString();

        return view('master-data.work-locations', ['workLocations' => $workLocations]);
    }

    public function storeWorkLocation(StoreWorkLocationRequest $request): RedirectResponse
    {
        $workLocation = WorkLocation::create($request->validated());
        $this->logCreated($workLocation);

        return redirect()->route('master-data.work-locations')->with('success', 'Work location created successfully.');
    }

    public function updateWorkLocation(StoreWorkLocationRequest $request, WorkLocation $workLocation): RedirectResponse
    {
        $oldData = $workLocation->attributesToArray();
        $workLocation->update($request->validated());
        $this->logUpdated($workLocation, $oldData);

        return redirect()->route('master-data.work-locations')->with('success', 'Work location updated successfully.');
    }

    public function destroyWorkLocation(WorkLocation $workLocation): RedirectResponse
    {
        $oldData = $workLocation->attributesToArray();
        $workLocation->delete();
        $this->logDeleted($workLocation, $oldData);

        return redirect()->route('master-data.work-locations')->with('success', 'Work location deleted successfully.');
    }

    public function departments(Request $request): View
    {
        $departments = ListSearchService::apply(Department::with('company'), $request, ['name'], [
            'company' => ['name'],
        ])->paginate(5)->withQueryString();

        return view('master-data.departments', [
            'departments' => $departments,
            'companiesList' => Company::all(),
        ]);
    }

    public function storeDepartment(StoreDepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());
        $this->logCreated($department);

        return redirect()->route('master-data.departments')->with('success', 'Department created successfully.');
    }

    public function updateDepartment(StoreDepartmentRequest $request, Department $department): RedirectResponse
    {
        $oldData = $department->attributesToArray();
        $department->update($request->validated());
        $this->logUpdated($department, $oldData);

        return redirect()->route('master-data.departments')->with('success', 'Department updated successfully.');
    }

    public function destroyDepartment(Department $department): RedirectResponse
    {
        $oldData = $department->attributesToArray();
        $department->delete();
        $this->logDeleted($department, $oldData);

        return redirect()->route('master-data.departments')->with('success', 'Department deleted successfully.');
    }

    public function divisions(Request $request): View
    {
        $divisions = ListSearchService::apply(Division::with('department'), $request, ['name'], [
            'department' => ['name'],
        ])->paginate(5)->withQueryString();

        return view('master-data.divisions', [
            'divisions' => $divisions,
            'departmentsList' => Department::all(),
        ]);
    }

    public function storeDivision(StoreDivisionRequest $request): RedirectResponse
    {
        $division = Division::create($request->validated());
        $this->logCreated($division);

        return redirect()->route('master-data.divisions')->with('success', 'Division created successfully.');
    }

    public function updateDivision(StoreDivisionRequest $request, Division $division): RedirectResponse
    {
        $oldData = $division->attributesToArray();
        $division->update($request->validated());
        $this->logUpdated($division, $oldData);

        return redirect()->route('master-data.divisions')->with('success', 'Division updated successfully.');
    }

    public function destroyDivision(Division $division): RedirectResponse
    {
        $oldData = $division->attributesToArray();
        $division->delete();
        $this->logDeleted($division, $oldData);

        return redirect()->route('master-data.divisions')->with('success', 'Division deleted successfully.');
    }

    public function sections(Request $request): View
    {
        $sections = ListSearchService::apply(Section::with('division'), $request, ['name'], [
            'division' => ['name'],
        ])->paginate(5)->withQueryString();

        return view('master-data.sections', [
            'sections' => $sections,
            'divisionsList' => Division::all(),
        ]);
    }

    public function storeSection(StoreSectionRequest $request): RedirectResponse
    {
        $section = Section::create($request->validated());
        $this->logCreated($section);

        return redirect()->route('master-data.sections')->with('success', 'Section created successfully.');
    }

    public function updateSection(StoreSectionRequest $request, Section $section): RedirectResponse
    {
        $oldData = $section->attributesToArray();
        $section->update($request->validated());
        $this->logUpdated($section, $oldData);

        return redirect()->route('master-data.sections')->with('success', 'Section updated successfully.');
    }

    public function destroySection(Section $section): RedirectResponse
    {
        $oldData = $section->attributesToArray();
        $section->delete();
        $this->logDeleted($section, $oldData);

        return redirect()->route('master-data.sections')->with('success', 'Section deleted successfully.');
    }
}
