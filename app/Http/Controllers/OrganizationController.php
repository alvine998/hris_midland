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
        Company::create($request->validated());

        return redirect()->route('master-data.companies')->with('success', 'Company created successfully.');
    }

    public function updateCompany(StoreCompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return redirect()->route('master-data.companies')->with('success', 'Company updated successfully.');
    }

    public function destroyCompany(Company $company): RedirectResponse
    {
        $company->delete();

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
        WorkLocation::create($request->validated());

        return redirect()->route('master-data.work-locations')->with('success', 'Work location created successfully.');
    }

    public function updateWorkLocation(StoreWorkLocationRequest $request, WorkLocation $workLocation): RedirectResponse
    {
        $workLocation->update($request->validated());

        return redirect()->route('master-data.work-locations')->with('success', 'Work location updated successfully.');
    }

    public function destroyWorkLocation(WorkLocation $workLocation): RedirectResponse
    {
        $workLocation->delete();

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
        Department::create($request->validated());

        return redirect()->route('master-data.departments')->with('success', 'Department created successfully.');
    }

    public function updateDepartment(StoreDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->route('master-data.departments')->with('success', 'Department updated successfully.');
    }

    public function destroyDepartment(Department $department): RedirectResponse
    {
        $department->delete();

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
        Division::create($request->validated());

        return redirect()->route('master-data.divisions')->with('success', 'Division created successfully.');
    }

    public function updateDivision(StoreDivisionRequest $request, Division $division): RedirectResponse
    {
        $division->update($request->validated());

        return redirect()->route('master-data.divisions')->with('success', 'Division updated successfully.');
    }

    public function destroyDivision(Division $division): RedirectResponse
    {
        $division->delete();

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
        Section::create($request->validated());

        return redirect()->route('master-data.sections')->with('success', 'Section created successfully.');
    }

    public function updateSection(StoreSectionRequest $request, Section $section): RedirectResponse
    {
        $section->update($request->validated());

        return redirect()->route('master-data.sections')->with('success', 'Section updated successfully.');
    }

    public function destroySection(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route('master-data.sections')->with('success', 'Section deleted successfully.');
    }
}
