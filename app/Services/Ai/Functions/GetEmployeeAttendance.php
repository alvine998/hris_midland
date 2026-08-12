<?php

namespace App\Services\Ai\Functions;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;
use Illuminate\Support\Carbon;

class GetEmployeeAttendance
{
    /**
     * Get attendance records for any employee by name or NIP on a given date.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string  $employee  Employee name or NIP.
     * @param  string|null  $date  Date in YYYY-MM-DD format (defaults to today).
     * @return array{date: string, records: array}
     */
    #[WAFunction('get_employee_attendance', 'Get attendance records for any employee by name or NIP on a specific date', 'attendance.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('employee', 'string', 'Employee name or NIP', required: true)]
        string $employee,
        #[WAFunctionParam('date', 'string', 'Date in YYYY-MM-DD format. Defaults to today.', required: false)]
        ?string $date = null,
    ): array {
        $targetDate = $date !== null ? Carbon::parse($date) : Carbon::today();

        $emp = Employee::where('name', 'like', '%'.$employee.'%')
            ->orWhere('nip', 'like', '%'.$employee.'%')
            ->first();

        if ($emp === null) {
            return [
                'date' => $targetDate->toDateString(),
                'records' => [],
                'message' => "No employee found matching '{$employee}'.",
            ];
        }

        $attendances = Attendance::where('employee_id', $emp->id)
            ->whereDate('clock_in', $targetDate)
            ->get();

        return [
            'date' => $targetDate->toDateString(),
            'employee' => $emp->name,
            'nip' => $emp->nip,
            'records' => $attendances->map(fn (Attendance $a) => [
                'clock_in' => $a->clock_in?->toDateTimeString(),
                'clock_out' => $a->clock_out?->toDateTimeString(),
                'status' => $a->status,
                'work_hours' => $a->work_hours,
                'location_in' => $a->location_in,
                'location_out' => $a->location_out,
            ])->toArray(),
        ];
    }
}
