<?php

namespace App\Services\Ai\Functions;

use App\Models\Attendance;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;
use Illuminate\Support\Carbon;

class GetTodayAttendanceSummary
{
    #[WAFunction(
        name: 'get_today_attendance_summary',
        description: 'Get today attendance summary for all employees. Shows who is present, absent, late, or on leave.',
        permission: 'attendances.view'
    )]
    public function execute(
        #[WAFunctionParam(name: 'date', type: 'string', description: 'Date in YYYY-MM-DD format. Defaults to today.', required: false)]
        ?string $date = null,
    ): array {
        $date = $date ? Carbon::parse($date) : now();

        $attendances = Attendance::with(['employee.department'])
            ->whereDate('date', $date)
            ->get();

        $summary = [
            'date' => $date->format('Y-m-d'),
            'total_records' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
            'employees' => $attendances->map(fn ($a) => [
                'name' => $a->employee?->name ?? 'Unknown',
                'nip' => $a->employee?->nip ?? '-',
                'department' => $a->employee?->department?->name ?? '-',
                'status' => $a->status,
                'clock_in' => $a->clock_in ? Carbon::parse($a->clock_in)->format('H:i') : '-',
                'clock_out' => $a->clock_out ? Carbon::parse($a->clock_out)->format('H:i') : '-',
            ])->toArray(),
        ];

        return $summary;
    }
}
