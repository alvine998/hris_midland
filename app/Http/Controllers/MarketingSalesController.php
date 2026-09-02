<?php

namespace App\Http\Controllers;

use App\Models\ClosingCustomer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectStock;
use App\Models\SurveyCustomer;
use Illuminate\View\View;

class MarketingSalesController extends Controller
{
    public function salesPerformance(): View
    {
        $metrics = [
            'totalLeads' => Lead::count(),
            'convertedLeads' => Lead::where('status', 'converted')->count(),
            'totalSurveys' => SurveyCustomer::count(),
            'completedSurveys' => SurveyCustomer::where('status', 'completed')->count(),
            'totalClosings' => ClosingCustomer::count(),
            'completedClosings' => ClosingCustomer::where('status', 'completed')->count(),
            'totalRevenue' => (float) ClosingCustomer::where('status', 'completed')->sum('amount'),
            'conversionRate' => Lead::count() > 0 ? round((ClosingCustomer::count() / max(Lead::count(), 1)) * 100, 1) : 0,
        ];

        $leadsByStatus = Lead::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $leadsBySource = Lead::selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->all();

        $closingsByPayment = ClosingCustomer::selectRaw('payment_method, COUNT(*) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->all();

        $monthlyClosings = ClosingCustomer::selectRaw("DATE_FORMAT(closing_date, '%Y-%m') as month, COUNT(*) as total, SUM(amount) as revenue")
            ->whereNotNull('closing_date')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get();

        $topProjects = Project::withCount(['closingCustomers', 'leads'])
            ->orderByDesc('closing_customers_count')
            ->limit(5)
            ->get();

        return view('marketing-sales.sales-performance', compact('metrics', 'leadsByStatus', 'leadsBySource', 'closingsByPayment', 'monthlyClosings', 'topProjects'));
    }

    public function marketingPerformance(): View
    {
        $metrics = [
            'totalLeads' => Lead::count(),
            'newLeads' => Lead::where('status', 'new')->count(),
            'qualifiedLeads' => Lead::where('status', 'qualified')->count(),
            'surveyScheduled' => Lead::where('status', 'survey_scheduled')->count(),
            'totalSurveys' => SurveyCustomer::count(),
            'highInterest' => SurveyCustomer::where('interest_level', 'high')->count(),
            'avgRating' => round((float) SurveyCustomer::avg('rating'), 1),
            'projectStocks' => ProjectStock::count(),
            'availableStocks' => ProjectStock::where('status', 'available')->count(),
        ];

        $leadsBySource = Lead::selectRaw('source, COUNT(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->all();

        $surveyByInterest = SurveyCustomer::selectRaw('interest_level, COUNT(*) as total')
            ->groupBy('interest_level')
            ->pluck('total', 'interest_level')
            ->all();

        $surveyByRating = SurveyCustomer::selectRaw('rating, COUNT(*) as total')
            ->whereNotNull('rating')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->all();

        $stockByStatus = ProjectStock::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $leadsTrend = Lead::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->limit(6)
            ->get();

        return view('marketing-sales.marketing-performance', compact('metrics', 'leadsBySource', 'surveyByInterest', 'surveyByRating', 'stockByStatus', 'leadsTrend'));
    }
}
