@extends('layouts.app')

@section('title', 'Operation & Construction Overview')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Operation & Construction — Overview</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Summary of projects, progress and property stock availability.</p>
</div>

{{-- Metrics --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['totalProjects']) }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">Total Projects</p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $metrics['ongoingProjects'] }} ongoing · {{ $metrics['planningProjects'] }} planning · {{ $metrics['completedProjects'] }} completed</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $metrics['avgProgress'] }}%</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">Average Progress</p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Across all projects</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['totalStocks']) }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">Property Units</p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $metrics['availableStocks'] }} available · {{ $metrics['soldStocks'] }} sold</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center mb-4">
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['availableStocks']) }}</p>
        <p class="text-sm text-gray-600 dark:text-gray-400">Available Stock</p>
        <a href="{{ route('marketing-sales.project-stocks.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">View stock</a>
    </div>
</div>

{{-- Status breakdown + Progress --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Projects by Status</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Distribution of project lifecycle status</p>
        <div class="space-y-3">
            @php($colors = ['planning'=>'bg-gray-200 dark:bg-gray-600','ongoing'=>'bg-indigo-500','completed'=>'bg-green-500','on_hold'=>'bg-amber-500','cancelled'=>'bg-red-500'])
            @forelse($statusBreakdown as $status => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ $colors[$status] ?? 'bg-gray-400' }}"></span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_',' ',$status) }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No projects yet.</p>
            @endforelse
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Progress Distribution</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">How many projects in each progress range</p>
        @php($max = max($progressDistribution['values']) ?: 1)
        <div class="space-y-3">
            @foreach($progressDistribution['labels'] as $i => $label)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $progressDistribution['values'][$i] }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ ($progressDistribution['values'][$i]/$max)*100 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Recent Projects --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Projects</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Latest projects in operation & construction</p>
        </div>
        <a href="{{ route('operation-construction.projects.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Project</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Company</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Progress</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-900 dark:text-white">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($projects as $project)
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->code ?? '-' }} · {{ $project->location ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $project->company?->name ?? '-' }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_',' ',$project->status) }}</span></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-24 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500" style="width: {{ $project->progress }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $project->progress }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('operation-construction.projects.show', $project) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No projects found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
