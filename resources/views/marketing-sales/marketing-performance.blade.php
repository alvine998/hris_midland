@extends('layouts.app')

@section('title', 'Marketing Performance')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Marketing Performance</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Lead generation, survey interest and property stock insights.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Leads</p>
        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['totalLeads']) }}</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $metrics['newLeads'] }} new · {{ $metrics['qualifiedLeads'] }} qualified</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Surveys</p>
        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['totalSurveys']) }}</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $metrics['highInterest'] }} high interest</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Avg Rating</p>
        <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $metrics['avgRating'] ?: '-' }} <span class="text-base font-medium">/ 5</span></p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Customer survey rating</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock</p>
        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['projectStocks']) }}</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $metrics['availableStocks'] }} available</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Leads by Source</h3>
        @forelse($leadsBySource as $source=>$count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_',' ',$source ?? 'Unknown') ?: 'Unknown' }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No data</p>
        @endforelse
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Survey by Interest Level</h3>
        @forelse($surveyByInterest as $level=>$count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $level ?? '—' }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No data</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Survey Rating Distribution</h3>
        @forelse($surveyByRating as $rating=>$count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span class="text-sm text-gray-600 dark:text-gray-400">⭐ {{ $rating }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No ratings yet.</p>
        @endforelse
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Property Stock by Status</h3>
        @forelse($stockByStatus as $status=>$count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $status }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No stock data.</p>
        @endforelse
        <div class="mt-4">
            <a href="{{ route('marketing-sales.project-stocks.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Manage Project Stock →</a>
        </div>
    </div>
</div>

<div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Leads Trend (by month)</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700"><th class="text-left px-3 py-2 font-semibold">Month</th><th class="text-right px-3 py-2 font-semibold">Leads</th></tr></thead>
            <tbody>
            @forelse($leadsTrend as $row)
                <tr class="border-b border-gray-100 dark:border-gray-700"><td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row->month }}</td><td class="px-3 py-2 text-right font-bold">{{ $row->total }}</td></tr>
            @empty
                <tr><td colspan="2" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">No trend data yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
