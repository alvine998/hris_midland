<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Feedback360;
use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $half = (int) $request->input('half', now()->month <= 6 ? 1 : 2);

        $startDate = $half === 1 ? "{$year}-01-01" : "{$year}-07-01";
        $endDate = $half === 1 ? "{$year}-06-30" : "{$year}-12-31";

        $kpiAggregates = Kpi::select(
            'employee_id',
            DB::raw('AVG(score) as avg_score'),
            DB::raw('COUNT(*) as total_kpis')
        )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        $feedbackAggregates = Feedback360::select(
            'employee_id',
            DB::raw('AVG(overall_score) as avg_score'),
            DB::raw('COUNT(*) as total_feedbacks')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'submitted')
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        $employeeIds = $kpiAggregates->keys()
            ->merge($feedbackAggregates->keys())
            ->unique()
            ->sort()
            ->values();

        $employees = Employee::with('department')
            ->whereIn('id', $employeeIds)
            ->orderBy('name')
            ->get();

        $reportData = $employees->map(function ($employee) use ($kpiAggregates, $feedbackAggregates) {
            $kpiData = $kpiAggregates->get($employee->id);
            $feedbackData = $feedbackAggregates->get($employee->id);

            return [
                'employee' => $employee,
                'kpi_count' => $kpiData?->total_kpis ?? 0,
                'kpi_avg_score' => $kpiData ? round((float) $kpiData->avg_score, 2) : null,
                'feedback_count' => $feedbackData?->total_feedbacks ?? 0,
                'feedback_avg_score' => $feedbackData ? round((float) $feedbackData->avg_score, 2) : null,
            ];
        });

        $totalEmployeesWithKpis = $kpiAggregates->count();
        $avgKpiScore = $kpiAggregates->avg('avg_score');
        $totalFeedbackSubmissions = $feedbackAggregates->sum('total_feedbacks');
        $avgFeedbackScore = $feedbackAggregates->avg('avg_score');

        $availableYears = range(now()->year, now()->subYears(4)->year);

        return view('performance.report.index', compact(
            'reportData',
            'year',
            'half',
            'totalEmployeesWithKpis',
            'avgKpiScore',
            'totalFeedbackSubmissions',
            'avgFeedbackScore',
            'availableYears'
        ));
    }
}
