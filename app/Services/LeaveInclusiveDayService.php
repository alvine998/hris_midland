<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\LeaveRequest;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;

class LeaveInclusiveDayService
{
    public function calculate(CarbonInterface $startDate, CarbonInterface $endDate): int
    {
        $start = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();
        $baseDays = $start->diffInDays($end) + 1;

        $holidayDays = Holiday::query()
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->get()
            ->flatMap(function (Holiday $holiday) use ($start, $end) {
                $holidayStart = $holiday->start_date->copy()->startOfDay()->greaterThan($start) ? $holiday->start_date->copy()->startOfDay() : $start;
                $holidayEnd = $holiday->end_date->copy()->startOfDay()->lessThan($end) ? $holiday->end_date->copy()->startOfDay() : $end;

                return collect(CarbonPeriod::create($holidayStart, $holidayEnd))->map(fn ($date) => $date->toDateString());
            })
            ->unique()
            ->count();

        return (int) max(0, $baseDays - $holidayDays);
    }

    public function recalculateApprovedOverlapping(CarbonInterface|string $startDate, CarbonInterface|string $endDate): void
    {
        LeaveRequest::query()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->chunkById(100, function ($leaveRequests): void {
                foreach ($leaveRequests as $leaveRequest) {
                    $leaveRequest->forceFill([
                        'inclusive_days' => $this->calculate($leaveRequest->start_date, $leaveRequest->end_date),
                    ])->saveQuietly();
                }
            });
    }
}
