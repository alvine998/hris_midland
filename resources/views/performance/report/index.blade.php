@extends('layouts.app')

@section('title', 'Performance Report - ' . config('app.name'))

@section('content')
@php
    $periodLabel = $half === 1 ? 'H1 (Jan - Jun)' : 'H2 (Jul - Dec)';
    $quickLinks = [
        ['label' => 'This H1', 'year' => now()->year, 'half' => 1, 'active' => (int) $year === (int) now()->year && (int) $half === 1],
        ['label' => 'This H2', 'year' => now()->year, 'half' => 2, 'active' => (int) $year === (int) now()->year && (int) $half === 2],
        ['label' => 'Last H1', 'year' => now()->subYear()->year, 'half' => 1, 'active' => (int) $year === (int) now()->subYear()->year && (int) $half === 1],
        ['label' => 'Last H2', 'year' => now()->subYear()->year, 'half' => 2, 'active' => (int) $year === (int) now()->subYear()->year && (int) $half === 2],
    ];
    $hasFilter = request()->hasAny(['year', 'half']);
@endphp

<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 overflow-hidden mb-6">
    {{-- Header --}}
    <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Performance Report</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $year }} {{ $periodLabel }} &middot; KPI & 360 Feedback results</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2 shrink-0">
                @foreach ($quickLinks as $link)
                    <a href="{{ route('performance.report', ['year' => $link['year'], 'half' => $link['half']]) }}" class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-150 {{ $link['active'] ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('performance.report') }}" class="px-6 py-3.5 bg-gray-50/60 dark:bg-gray-800/40">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Selects --}}
            <div class="flex items-center gap-2">
                <label for="year" class="text-sm font-medium text-gray-700 dark:text-gray-300">Year</label>
                <select name="year" id="year" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white transition-colors">
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" @selected((int) $year === $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="half" class="text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
                <select name="half" id="half" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white transition-colors">
                    <option value="1" @selected((int) $half === 1)>H1 (Jan - Jun)</option>
                    <option value="2" @selected((int) $half === 2)>H2 (Jul - Dec)</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Apply
                </button>
                @if ($hasFilter)
                    <a href="{{ route('performance.report') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>

            {{-- Active filter badge (mobile) --}}
            <div class="sm:hidden ml-auto">
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H5v12z"/></svg>
                    {{ $year }} {{ $periodLabel }}
                </span>
            </div>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalEmployeesWithKpis) }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Employees with KPIs</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $avgKpiScore ? number_format($avgKpiScore, 2) : '-' }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Average KPI Score</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalFeedbackSubmissions) }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">360 Feedbacks Submitted</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v3l-4-3H9a2 2 0 01-2-2v-1m10-7V5a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v3l4-3h4a2 2 0 002-2V8z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $avgFeedbackScore ? number_format($avgFeedbackScore, 2) : '-' }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Average 360 Score</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Employee</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Department</th>
                    <th class="text-center px-6 py-4 font-semibold text-gray-900 dark:text-white">KPIs</th>
                    <th class="text-center px-6 py-4 font-semibold text-gray-900 dark:text-white">Avg KPI Score</th>
                    <th class="text-center px-6 py-4 font-semibold text-gray-900 dark:text-white">360 Feedbacks</th>
                    <th class="text-center px-6 py-4 font-semibold text-gray-900 dark:text-white">Avg 360 Score</th>
                    <th class="text-center px-6 py-4 font-semibold text-gray-900 dark:text-white">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($reportData as $data)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $data['employee']->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                        {{ $data['employee']->department?->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                        {{ $data['kpi_count'] }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($data['kpi_avg_score'] !== null)
                            <span class="font-medium {{ $data['kpi_avg_score'] >= 4 ? 'text-emerald-600 dark:text-emerald-400' : ($data['kpi_avg_score'] >= 3 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ number_format($data['kpi_avg_score'], 2) }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                        {{ $data['feedback_count'] }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($data['feedback_avg_score'] !== null)
                            <span class="font-medium {{ $data['feedback_avg_score'] >= 4 ? 'text-emerald-600 dark:text-emerald-400' : ($data['feedback_avg_score'] >= 3 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ number_format($data['feedback_avg_score'], 2) }}
                            </span>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $hasKpi = $data['kpi_count'] > 0;
                            $hasFeedback = $data['feedback_count'] > 0;
                        @endphp
                        @if ($hasKpi && $hasFeedback)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                Complete
                            </span>
                        @elseif ($hasKpi)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                No Feedback
                            </span>
                        @elseif ($hasFeedback)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                No KPIs
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                Incomplete
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        No performance data found for the selected period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
