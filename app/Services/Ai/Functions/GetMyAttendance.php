<?php

namespace App\Services\Ai\Functions;

use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;
use Carbon\Carbon;

class GetMyAttendance
{
    /**
     * Get the authenticated user's attendance records for a specific date.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $date  The date in YYYY-MM-DD format (defaults to today).
     * @return array{date: string, records: array}
     */
    #[WAFunction('get_my_attendance', 'Get my attendance records for a specific date')]
    public function execute(
        User $user,
        #[WAFunctionParam('date', 'string', 'Date in YYYY-MM-DD format', required: false)]
        ?string $date = null,
    ): array {
        $targetDate = $date !== null ? Carbon::parse($date) : Carbon::today();

        $employee = $user->employee;

        if ($employee === null) {
            return [
                'date' => $targetDate->toDateString(),
                'records' => [],
                'message' => 'No employee record found for your account.',
            ];
        }

        $attendances = $employee->attendances()
            ->whereDate('clock_in', $targetDate)
            ->get();

        return [
            'date' => $targetDate->toDateString(),
            'records' => $attendances->map(fn ($attendance) => [
                'clock_in' => $attendance->clock_in?->toDateTimeString(),
                'clock_out' => $attendance->clock_out?->toDateTimeString(),
                'status' => $attendance->status,
                'work_hours' => $attendance->work_hours,
                'location_in' => $attendance->location_in,
                'location_out' => $attendance->location_out,
            ])->toArray(),
        ];
    }
}
