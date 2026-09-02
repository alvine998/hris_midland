@extends('layouts.app')

@section('title', 'Sales Performance')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Performance</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Lead conversion, closing rate and revenue overview.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Leads</p>
        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['totalLeads']) }}</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ number_format($metrics['convertedLeads']) }} converted</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Closings</p>
        <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['totalClosings']) }}</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ number_format($metrics['completedClosings']) }} completed</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Revenue</p>
        <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($metrics['totalRevenue'],0,',','.') }}</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">From completed closings</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Conversion Rate</p>
        <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $metrics['conversionRate'] }}%</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leads → Closings</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Leads by Status</h3>
        @forelse($leadsByStatus as $status=>$count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ str_replace('_',' ',$status) }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No data</p>
        @endforelse
    </div>
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
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Closings by Payment</h3>
        @forelse($closingsByPayment as $method=>$count)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span class="text-sm text-gray-600 dark:text-gray-400 uppercase">{{ $method ?? '—' }}</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No data</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Monthly Closings</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700"><th class="text-left px-3 py-2 font-semibold">Month</th><th class="text-right px-3 py-2 font-semibold">Closings</th><th class="text-right px-3 py-2 font-semibold">Revenue</th></tr></thead>
                <tbody>
                @forelse($monthlyClosings as $row)
                    <tr class="border-b border-gray-100 dark:border-gray-700"><td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $row->month }}</td><td class="px-3 py-2 text-right">{{ $row->total }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($row->revenue,0,',','.') }}</td></tr>
                @empty
                    <tr><td colspan="3" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">No closing data yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Top Projects by Closings</h3>
        @forelse($topProjects as $project)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $project->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->leads_count }} leads</p>
                </div>
                <span class="rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-3 py-1 text-xs font-bold text-indigo-700 dark:text-indigo-300">{{ $project->closing_customers_count }} closings</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No project data.</p>
        @endforelse
    </div>
</div>
@endsection
