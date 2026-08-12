<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $totalEmployees = Employee::count();
        $totalUsers = User::count();

        $recentCompanies = Company::latest()
            ->take(5)
            ->get();

        $planDistribution = Company::selectRaw('plan, count(*) as total')
            ->groupBy('plan')
            ->orderByDesc('total')
            ->get();

        return view('admin.dashboard', compact(
            'totalCompanies',
            'activeCompanies',
            'totalEmployees',
            'totalUsers',
            'recentCompanies',
            'planDistribution',
        ));
    }
}
