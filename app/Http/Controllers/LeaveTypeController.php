<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveTypeRequest;
use App\Models\LeaveType;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function index(Request $request): View
    {
        $leaveTypes = ListSearchService::apply(LeaveType::query(), $request, ['name'])
            ->paginate(10)
            ->withQueryString();

        return view('master-data.leave-types', ['leaveTypes' => $leaveTypes]);
    }

    public function store(StoreLeaveTypeRequest $request): RedirectResponse
    {
        $leaveType = LeaveType::create($request->validated());
        $this->logCreated($leaveType, 'Leave Management');

        return back()->with('success', 'Leave type created successfully.');
    }

    public function update(StoreLeaveTypeRequest $request, LeaveType $leaveType): RedirectResponse
    {
        $oldData = $leaveType->attributesToArray();
        $leaveType->update($request->validated());
        $this->logUpdated($leaveType, $oldData, 'Leave Management');

        return back()->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        $oldData = $leaveType->attributesToArray();
        $leaveType->delete();
        $this->logDeleted($leaveType, $oldData, 'Leave Management');

        return back()->with('success', 'Leave type deleted successfully.');
    }
}
