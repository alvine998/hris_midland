<?php

namespace App\Services\Ai\Functions;

use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;

class GetMyLeaveBalance
{
    /**
     * Get the current user's leave balance summary.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @return array{leave_balance: array|null}
     */
    #[WAFunction('get_my_leave_balance', 'Get my current leave balance summary')]
    public function execute(
        User $user,
    ): array {
        $employee = $user->employee;

        if ($employee === null) {
            return [
                'leave_balance' => null,
                'message' => 'No employee record found for your account.',
            ];
        }

        $leaveBalance = $employee->leaveBalance;

        if ($leaveBalance === null) {
            return [
                'leave_balance' => null,
                'message' => 'No leave balance record found for your account.',
            ];
        }

        return [
            'leave_balance' => [
                'total' => (int) $leaveBalance->total,
                'used' => (int) $leaveBalance->used,
                'remaining' => (int) $leaveBalance->remaining,
                'extra' => (int) $leaveBalance->extra,
            ],
        ];
    }
}
