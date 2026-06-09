<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveApprovalRequest;
use App\Models\LeaveApproval;
use Illuminate\Http\RedirectResponse;

class LeaveApprovalController extends Controller
{
    public function store(StoreLeaveApprovalRequest $request): RedirectResponse
    {
        LeaveApproval::create($request->validated());

        return back()->with('success', 'Leave approval created successfully.');
    }

    public function update(StoreLeaveApprovalRequest $request, LeaveApproval $leaveApproval): RedirectResponse
    {
        $leaveApproval->update($request->validated());

        return back()->with('success', 'Leave approval updated successfully.');
    }

    public function destroy(LeaveApproval $leaveApproval): RedirectResponse
    {
        $leaveApproval->delete();

        return back()->with('success', 'Leave approval deleted successfully.');
    }
}
