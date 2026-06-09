<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeShiftRequest;
use App\Models\EmployeeShift;
use Illuminate\Http\RedirectResponse;

class EmployeeShiftController extends Controller
{
    public function store(StoreEmployeeShiftRequest $request): RedirectResponse
    {
        EmployeeShift::create($request->validated());

        return back()->with('success', 'Employee shift created successfully.');
    }

    public function update(StoreEmployeeShiftRequest $request, EmployeeShift $employeeShift): RedirectResponse
    {
        $employeeShift->update($request->validated());

        return back()->with('success', 'Employee shift updated successfully.');
    }

    public function destroy(EmployeeShift $employeeShift): RedirectResponse
    {
        $employeeShift->delete();

        return back()->with('success', 'Employee shift deleted successfully.');
    }
}
