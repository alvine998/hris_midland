<?php

namespace App\Services\Ai\Functions;

use App\Models\LeaveApproval;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;

class GetPendingLeaveApprovals
{
    /**
     * List leave approvals pending action.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @return array{count: int, approvals: array}
     */
    #[WAFunction('get_pending_leave_approvals', 'List leave approvals that are pending action', 'leave-request.view')]
    public function execute(
        User $user,
    ): array {
        $approvals = LeaveApproval::with(['leaveRequest.employee.department', 'approver'])
            ->where('status', 'pending')
            ->latest()
            ->limit(20)
            ->get();

        return [
            'count' => $approvals->count(),
            'approvals' => $approvals->map(fn (LeaveApproval $la) => [
                'employee' => $la->leaveRequest?->employee?->name,
                'department' => $la->leaveRequest?->employee?->department?->name,
                'title' => $la->leaveRequest?->title,
                'start_date' => $la->leaveRequest?->start_date?->toDateString(),
                'end_date' => $la->leaveRequest?->end_date?->toDateString(),
                'inclusive_days' => $la->leaveRequest?->inclusive_days,
                'approver' => $la->approver?->name,
                'status' => $la->status,
            ])->toArray(),
        ];
    }
}
