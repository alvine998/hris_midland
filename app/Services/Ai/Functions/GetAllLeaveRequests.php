<?php

namespace App\Services\Ai\Functions;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetAllLeaveRequests
{
    /**
     * List all leave requests across the organisation with optional filters.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $status  Filter by status (pending/approved/rejected).
     * @param  string|null  $employee  Filter by employee name or NIP.
     * @return array{count: int, leave_requests: array}
     */
    #[WAFunction('get_all_leave_requests', 'List all leave requests across the organisation with optional status and employee filters', 'leave-request.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('status', 'string', 'Leave request status filter', enum: ['pending', 'approved', 'rejected'], required: false)]
        ?string $status = null,
        #[WAFunctionParam('employee', 'string', 'Filter by employee name or NIP', required: false)]
        ?string $employee = null,
    ): array {
        $query = LeaveRequest::with(['employee.department', 'leaveType']);

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($employee !== null && $employee !== '') {
            $query->whereHas('employee', function ($q) use ($employee): void {
                $q->where('name', 'like', '%'.$employee.'%')
                    ->orWhere('nip', 'like', '%'.$employee.'%');
            });
        }

        $requests = $query->latest()->limit(20)->get();

        return [
            'count' => $requests->count(),
            'leave_requests' => $requests->map(fn (LeaveRequest $lr) => [
                'employee' => $lr->employee?->name,
                'nip' => $lr->employee?->nip,
                'department' => $lr->employee?->department?->name,
                'leave_type' => $lr->leaveType?->name,
                'title' => $lr->title,
                'start_date' => $lr->start_date?->toDateString(),
                'end_date' => $lr->end_date?->toDateString(),
                'inclusive_days' => $lr->inclusive_days,
                'reason' => $lr->reason,
                'status' => $lr->status,
            ])->toArray(),
        ];
    }
}
