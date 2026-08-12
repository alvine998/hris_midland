<?php

namespace App\Services\Ai\Functions;

use App\Models\Attendance;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use Illuminate\Support\Carbon;

class GetDashboardSummary
{
    /**
     * Get an overall HR dashboard summary with key organisation-wide metrics.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @return array<string, mixed>
     */
    #[WAFunction('get_dashboard_summary', 'Get an overall HR dashboard summary with key organisation-wide metrics', 'dashboard.view')]
    public function execute(
        User $user,
    ): array {
        $today = Carbon::today();
        $nextThirtyDays = $today->copy()->addDays(30);

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $inactiveEmployees = Employee::where('status', 'inactive')->count();
        $activeToday = Attendance::whereDate('clock_in', $today)
            ->distinct('employee_id')
            ->count('employee_id');
        $expiringContracts = Contract::whereBetween('end_date', [$today, $nextThirtyDays])->count();

        $attendanceToday = Attendance::whereDate('clock_in', $today)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $departments = Department::withCount(['employees as active_employees_count' => function ($query): void {
            $query->where('status', 'active');
        }])
            ->orderByDesc('active_employees_count')
            ->limit(5)
            ->get();

        return [
            'metrics' => [
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
                'inactive_employees' => $inactiveEmployees,
                'active_today' => $activeToday,
                'departments' => Department::count(),
                'expiring_contracts_30_days' => $expiringContracts,
            ],
            'attendance_today' => [
                'present' => (int) ($attendanceToday['present'] ?? 0),
                'late' => (int) ($attendanceToday['late'] ?? 0),
                'absent' => (int) ($attendanceToday['absent'] ?? 0),
                'on_leave' => (int) ($attendanceToday['on_leave'] ?? 0),
            ],
            'leave_balance_totals' => [
                'used' => (int) LeaveBalance::sum('used'),
                'remaining' => (int) LeaveBalance::sum('remaining'),
                'extra' => (int) LeaveBalance::sum('extra'),
            ],
            'top_departments' => $departments->map(fn (Department $d) => [
                'name' => $d->name,
                'active_employees' => $d->active_employees_count,
            ])->toArray(),
        ];
    }
}
