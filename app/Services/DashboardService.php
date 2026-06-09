<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\WorkLocation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function data(): array
    {
        $today = Carbon::today();
        $nextThirtyDays = $today->copy()->addDays(30);

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $activeToday = Attendance::whereDate('clock_in', $today)
            ->distinct('employee_id')
            ->count('employee_id');
        $expiringContracts = Contract::whereBetween('end_date', [$today, $nextThirtyDays])->count();

        return [
            'metrics' => [
                'totalEmployees' => $totalEmployees,
                'activeToday' => $activeToday,
                'departments' => Department::count(),
                'expiringContracts' => $expiringContracts,
                'activeEmployees' => $activeEmployees,
            ],
            'charts' => [
                'departmentEmployees' => $this->departmentEmployees(),
                'attendanceStatus' => $this->attendanceStatus(),
                'employeeStatus' => $this->employeeStatus(),
                'leaveBalance' => $this->leaveBalance(),
                'workLocations' => $this->workLocations(),
                'monthlyJoins' => $this->monthlyJoins(),
            ],
            'recentEmployees' => Employee::with(['department', 'jobPosition'])
                ->latest()
                ->limit(4)
                ->get(),
        ];
    }

    private function departmentEmployees(): array
    {
        $departments = Department::withCount(['employees as active_employees_count' => function ($query) {
            $query->where('status', 'active');
        }])
            ->orderByDesc('active_employees_count')
            ->limit(8)
            ->get();

        return $this->labelsAndValues($departments, 'name', 'active_employees_count');
    }

    private function attendanceStatus(): array
    {
        $attendances = Attendance::query()
            ->whereBetween('clock_in', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        return $this->labelsAndValues($attendances, 'status', 'total');
    }

    private function employeeStatus(): array
    {
        $statuses = Employee::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        return $this->labelsAndValues($statuses, 'status', 'total');
    }

    private function leaveBalance(): array
    {
        return [
            'labels' => ['Used', 'Remaining', 'Extra'],
            'values' => [
                (int) LeaveBalance::sum('used'),
                (int) LeaveBalance::sum('remaining'),
                (int) LeaveBalance::sum('extra'),
            ],
        ];
    }

    private function workLocations(): array
    {
        $locations = WorkLocation::withCount('employees')
            ->orderByDesc('employees_count')
            ->limit(6)
            ->get();

        return $this->labelsAndValues($locations, 'name', 'employees_count');
    }

    private function monthlyJoins(): array
    {
        $months = collect(range(5, 0))
            ->map(function (int $monthsAgo) {
                $month = Carbon::now()->subMonths($monthsAgo);

                return [
                    'key' => $month->format('Y-m'),
                    'label' => $month->format('M'),
                    'value' => 0,
                ];
            })
            ->keyBy('key');

        $joinCounts = Employee::whereNotNull('join_date')
            ->whereDate('join_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->get(['join_date'])
            ->countBy(fn (Employee $employee) => $employee->join_date->format('Y-m'));

        return [
            'labels' => $months->pluck('label')->values()->all(),
            'values' => $months->keys()
                ->map(fn (string $key) => (int) $joinCounts->get($key, 0))
                ->values()
                ->all(),
        ];
    }

    private function labelsAndValues(Collection $items, string $labelKey, string $valueKey): array
    {
        return [
            'labels' => $items->pluck($labelKey)->map(fn ($label) => ucfirst((string) $label))->values()->all(),
            'values' => $items->pluck($valueKey)->map(fn ($value) => (int) $value)->values()->all(),
        ];
    }
}
