<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeShiftRequest;
use App\Models\EmployeeShift;
use Illuminate\Http\RedirectResponse;

class EmployeeShiftController extends Controller
{
    public function store(StoreEmployeeShiftRequest $request): RedirectResponse
    {
        $employeeShift = EmployeeShift::create($request->validated());
        $this->logCreated($employeeShift);

        return back()->with('success', 'Employee shift created successfully.');
    }

    public function update(StoreEmployeeShiftRequest $request, EmployeeShift $employeeShift): RedirectResponse
    {
        $oldData = $employeeShift->attributesToArray();
        $employeeShift->update($request->validated());
        $this->logUpdated($employeeShift, $oldData);

        return back()->with('success', 'Employee shift updated successfully.');
    }

    public function destroy(EmployeeShift $employeeShift): RedirectResponse
    {
        $oldData = $employeeShift->attributesToArray();
        $employeeShift->delete();
        $this->logDeleted($employeeShift, $oldData);

        return back()->with('success', 'Employee shift deleted successfully.');
    }
}
