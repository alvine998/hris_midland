<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Contract;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\WorkLocation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SuperAdminOverviewService
{
    public function data(?int $divisionId = null): array
    {
        $division = $divisionId ? Division::with('department')->find($divisionId) : null;
        $today = Carbon::today();
        $nextThirtyDays = $today->copy()->addDays(30);

        return [
            'divisions' => $this->divisionOptions(),
            'selectedDivision' => $division,
            'metrics' => [
                'totalEmployees' => $this->employees($division)->count(),
                'activeEmployees' => $this->employees($division)->where('status', 'active')->count(),
                'presentToday' => $this->attendances($division)
                    ->whereDate('clock_in', $today)
                    ->distinct('employee_id')
                    ->count('employee_id'),
                'onLeaveToday' => $this->leaveRequests($division)
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->distinct('employee_id')
                    ->count('employee_id'),
                'pendingLeaves' => $this->leaveRequests($division)->where('status', 'pending')->count(),
                'expiringContracts' => $this->contracts($division)
                    ->whereBetween('end_date', [$today, $nextThirtyDays])
                    ->count(),
            ],
            'charts' => [
                'headcount' => $division ? $this->workLocationHeadcounts($division) : $this->divisionHeadcounts(),
                'attendanceStatus' => $this->attendanceStatus($division),
                'employeeStatus' => $this->employeeStatus($division),
                'monthlyJoins' => $this->monthlyJoins($division),
            ],
            'recentEmployees' => $this->recentEmployees($division),
        ];
    }

    private function divisionOptions(): Collection
    {
        return Division::query()
            ->with('department:id,name')
            ->withCount(['employees as active_employees_count' => fn ($query) => $query->where('status', 'active')])
            ->orderBy('name')
            ->get();
    }

    private function employees(?Division $division)
    {
        return Employee::query()
            ->when($division, fn ($query) => $query->where('division_id', $division->id));
    }

    private function attendances(?Division $division)
    {
        return Attendance::query()
            ->when($division, fn ($query) => $query->whereHas('employee', fn ($q) => $q->where('division_id', $division->id)));
    }

    private function leaveRequests(?Division $division)
    {
        return LeaveRequest::query()
            ->when($division, fn ($query) => $query->whereHas('employee', fn ($q) => $q->where('division_id', $division->id)));
    }

    private function contracts(?Division $division)
    {
        return Contract::query()
            ->when($division, fn ($query) => $query->whereHas('employee', fn ($q) => $q->where('division_id', $division->id)));
    }

    private function divisionHeadcounts(): array
    {
        $divisions = Division::query()
            ->withCount(['employees as active_employees_count' => fn ($query) => $query->where('status', 'active')])
            ->orderByDesc('active_employees_count')
            ->limit(10)
            ->get();

        return $this->labelsAndValues($divisions, 'name', 'active_employees_count');
    }

    private function workLocationHeadcounts(Division $division): array
    {
        $locations = WorkLocation::query()
            ->whereIn('id', $this->employees($division)->select('work_location_id'))
            ->withCount(['employees as active_employees_count' => fn ($query) => $query
                ->where('status', 'active')
                ->where('division_id', $division->id),
            ])
            ->orderByDesc('active_employees_count')
            ->get();

        return $this->labelsAndValues($locations, 'name', 'active_employees_count');
    }

    private function attendanceStatus(?Division $division): array
    {
        $attendances = $this->attendances($division)
            ->whereBetween('clock_in', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        return $this->labelsAndValues($attendances, 'status', 'total');
    }

    private function employeeStatus(?Division $division): array
    {
        $statuses = $this->employees($division)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        return $this->labelsAndValues($statuses, 'status', 'total');
    }

    private function monthlyJoins(?Division $division): array
    {
        $months = collect(range(5, 0))
            ->map(function (int $monthsAgo) {
                $month = Carbon::now()->subMonths($monthsAgo);

                return [
                    'key' => $month->format('Y-m'),
                    'label' => $month->format('M'),
                ];
            });

        $joinCounts = $this->employees($division)
            ->whereNotNull('join_date')
            ->whereDate('join_date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->get(['join_date'])
            ->countBy(fn (Employee $employee) => $employee->join_date->format('Y-m'));

        return [
            'labels' => $months->pluck('label')->values()->all(),
            'values' => $months
                ->pluck('key')
                ->map(fn (string $key) => (int) $joinCounts->get($key, 0))
                ->values()
                ->all(),
        ];
    }

    private function recentEmployees(?Division $division): Collection
    {
        return $this->employees($division)
            ->with(['division', 'department', 'jobPosition'])
            ->latest()
            ->limit(6)
            ->get();
    }

    private function labelsAndValues(Collection $items, string $labelKey, string $valueKey): array
    {
        return [
            'labels' => $items->pluck($labelKey)->map(fn ($label) => ucfirst((string) $label))->values()->all(),
            'values' => $items->pluck($valueKey)->map(fn ($value) => (int) $value)->values()->all(),
        ];
    }
}
