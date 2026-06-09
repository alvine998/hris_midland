<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractTypeRequest;
use App\Http\Requests\StoreEducationTypeRequest;
use App\Http\Requests\StoreFamilyTypeRequest;
use App\Http\Requests\StoreJobPositionRequest;
use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\StoreReligionRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Models\ContractType;
use App\Models\EducationType;
use App\Models\FamilyType;
use App\Models\JobPosition;
use App\Models\Level;
use App\Models\Module;
use App\Models\Religion;
use App\Models\Role;
use App\Services\ListSearchService;
use App\Services\RbacPermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferenceController extends Controller
{
    public function levels(Request $request): View
    {
        $levels = ListSearchService::apply(Level::query(), $request, ['name'])->paginate(5)->withQueryString();

        return view('master-data.levels', ['levels' => $levels]);
    }

    public function storeLevel(StoreLevelRequest $request): RedirectResponse
    {
        Level::create($request->validated());

        return redirect()->route('master-data.levels')->with('success', 'Level created successfully.');
    }

    public function updateLevel(StoreLevelRequest $request, Level $level): RedirectResponse
    {
        $level->update($request->validated());

        return redirect()->route('master-data.levels')->with('success', 'Level updated successfully.');
    }

    public function destroyLevel(Level $level): RedirectResponse
    {
        $level->delete();

        return redirect()->route('master-data.levels')->with('success', 'Level deleted successfully.');
    }

    public function religions(Request $request): View
    {
        $religions = ListSearchService::apply(Religion::query(), $request, ['name'])->paginate(5)->withQueryString();

        return view('master-data.religions', ['religions' => $religions]);
    }

    public function storeReligion(StoreReligionRequest $request): RedirectResponse
    {
        Religion::create($request->validated());

        return redirect()->route('master-data.religions')->with('success', 'Religion created successfully.');
    }

    public function updateReligion(StoreReligionRequest $request, Religion $religion): RedirectResponse
    {
        $religion->update($request->validated());

        return redirect()->route('master-data.religions')->with('success', 'Religion updated successfully.');
    }

    public function destroyReligion(Religion $religion): RedirectResponse
    {
        $religion->delete();

        return redirect()->route('master-data.religions')->with('success', 'Religion deleted successfully.');
    }

    public function jobPositions(Request $request): View
    {
        $jobPositions = ListSearchService::apply(JobPosition::with('level'), $request, ['name'], [
            'level' => ['name'],
        ])->paginate(5)->withQueryString();

        return view('master-data.job-positions', [
            'jobPositions' => $jobPositions,
            'levelsList' => Level::all(),
        ]);
    }

    public function storeJobPosition(StoreJobPositionRequest $request): RedirectResponse
    {
        JobPosition::create($request->validated());

        return redirect()->route('master-data.job-positions')->with('success', 'Job position created successfully.');
    }

    public function updateJobPosition(StoreJobPositionRequest $request, JobPosition $jobPosition): RedirectResponse
    {
        $jobPosition->update($request->validated());

        return redirect()->route('master-data.job-positions')->with('success', 'Job position updated successfully.');
    }

    public function destroyJobPosition(JobPosition $jobPosition): RedirectResponse
    {
        $jobPosition->delete();

        return redirect()->route('master-data.job-positions')->with('success', 'Job position deleted successfully.');
    }

    public function modules(Request $request): View
    {
        $modules = ListSearchService::apply(Module::query(), $request, ['name'])->paginate(5)->withQueryString();

        return view('master-data.modules', ['modules' => $modules]);
    }

    public function storeModule(StoreModuleRequest $request): RedirectResponse
    {
        Module::create($request->validated());

        return redirect()->route('master-data.modules')->with('success', 'Module created successfully.');
    }

    public function updateModule(StoreModuleRequest $request, Module $module): RedirectResponse
    {
        $module->update($request->validated());

        return redirect()->route('master-data.modules')->with('success', 'Module updated successfully.');
    }

    public function destroyModule(Module $module): RedirectResponse
    {
        $module->delete();

        return redirect()->route('master-data.modules')->with('success', 'Module deleted successfully.');
    }

    public function roles(Request $request): View
    {
        $roles = ListSearchService::apply(Role::query(), $request, ['name', 'description'])
            ->paginate(5)
            ->withQueryString();

        return view('master-data.roles', [
            'roles' => $roles,
            'permissionGroups' => RbacPermissionService::groups(),
        ]);
    }

    public function storeRole(StoreRoleRequest $request): RedirectResponse
    {
        Role::create($request->validated());

        return redirect()->route('master-data.roles')->with('success', 'Role created successfully.');
    }

    public function updateRole(StoreRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->validated());

        return redirect()->route('master-data.roles')->with('success', 'Role updated successfully.');
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('master-data.roles')->with('success', 'Role deleted successfully.');
    }

    public function contractTypes(Request $request): View
    {
        $contractTypes = ListSearchService::apply(ContractType::query(), $request, ['name'])->paginate(5)->withQueryString();

        return view('master-data.contract-types', ['contractTypes' => $contractTypes]);
    }

    public function storeContractType(StoreContractTypeRequest $request): RedirectResponse
    {
        ContractType::create($request->validated());

        return redirect()->route('master-data.contract-types')->with('success', 'Contract type created successfully.');
    }

    public function updateContractType(StoreContractTypeRequest $request, ContractType $contractType): RedirectResponse
    {
        $contractType->update($request->validated());

        return redirect()->route('master-data.contract-types')->with('success', 'Contract type updated successfully.');
    }

    public function destroyContractType(ContractType $contractType): RedirectResponse
    {
        $contractType->delete();

        return redirect()->route('master-data.contract-types')->with('success', 'Contract type deleted successfully.');
    }

    public function educationTypes(Request $request): View
    {
        $educationTypes = ListSearchService::apply(EducationType::query(), $request, ['name'])->paginate(5)->withQueryString();

        return view('master-data.education-types', ['educationTypes' => $educationTypes]);
    }

    public function storeEducationType(StoreEducationTypeRequest $request): RedirectResponse
    {
        EducationType::create($request->validated());

        return redirect()->route('master-data.education-types')->with('success', 'Education type created successfully.');
    }

    public function updateEducationType(StoreEducationTypeRequest $request, EducationType $educationType): RedirectResponse
    {
        $educationType->update($request->validated());

        return redirect()->route('master-data.education-types')->with('success', 'Education type updated successfully.');
    }

    public function destroyEducationType(EducationType $educationType): RedirectResponse
    {
        $educationType->delete();

        return redirect()->route('master-data.education-types')->with('success', 'Education type deleted successfully.');
    }

    public function familyTypes(Request $request): View
    {
        $familyTypes = ListSearchService::apply(FamilyType::query(), $request, ['name'])->paginate(5)->withQueryString();

        return view('master-data.family-types', ['familyTypes' => $familyTypes]);
    }

    public function storeFamilyType(StoreFamilyTypeRequest $request): RedirectResponse
    {
        FamilyType::create($request->validated());

        return redirect()->route('master-data.family-types')->with('success', 'Family type created successfully.');
    }

    public function updateFamilyType(StoreFamilyTypeRequest $request, FamilyType $familyType): RedirectResponse
    {
        $familyType->update($request->validated());

        return redirect()->route('master-data.family-types')->with('success', 'Family type updated successfully.');
    }

    public function destroyFamilyType(FamilyType $familyType): RedirectResponse
    {
        $familyType->delete();

        return redirect()->route('master-data.family-types')->with('success', 'Family type deleted successfully.');
    }
}
