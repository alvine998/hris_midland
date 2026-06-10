<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveApprovalRequest;
use App\Models\LeaveApproval;
use Illuminate\Http\RedirectResponse;

class LeaveApprovalController extends Controller
{
    public function store(StoreLeaveApprovalRequest $request): RedirectResponse
    {
        $leaveApproval = LeaveApproval::create($request->validated());
        $this->logCreated($leaveApproval, 'Leave Management');

        return back()->with('success', 'Leave approval created successfully.');
    }

    public function update(StoreLeaveApprovalRequest $request, LeaveApproval $leaveApproval): RedirectResponse
    {
        $oldData = $leaveApproval->attributesToArray();
        $leaveApproval->update($request->validated());
        $this->logUpdated($leaveApproval, $oldData, 'Leave Management');

        return back()->with('success', 'Leave approval updated successfully.');
    }

    public function destroy(LeaveApproval $leaveApproval): RedirectResponse
    {
        $oldData = $leaveApproval->attributesToArray();
        $leaveApproval->delete();
        $this->logDeleted($leaveApproval, $oldData, 'Leave Management');

        return back()->with('success', 'Leave approval deleted successfully.');
    }
}
