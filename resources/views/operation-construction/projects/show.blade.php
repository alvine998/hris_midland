@extends('layouts.app')

@section('title', 'Project Detail - '.$project->name)

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('operation-construction.projects.index') }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">← Back</a>
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $project->code ?? 'No code' }} · {{ $project->location ?? 'No location' }}</p>
    </div>
    <span class="ml-auto inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 capitalize">{{ str_replace('_',' ',$project->status) }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Project Information</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Company</dt>
                <dd class="font-medium text-gray-900 dark:text-white">{{ $project->company?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Manager</dt>
                <dd class="font-medium text-gray-900 dark:text-white">{{ $project->manager?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Start — End</dt>
                <dd class="font-medium text-gray-900 dark:text-white">{{ $project->start_date?->format('d M Y') ?? '-' }} — {{ $project->end_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Budget</dt>
                <dd class="font-medium text-gray-900 dark:text-white">{{ $project->budget ? 'Rp '.number_format($project->budget,0,',','.') : '-' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500 dark:text-gray-400">Progress</dt>
                <dd class="mt-2">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden"><div class="h-full bg-indigo-500" style="width: {{ $project->progress }}%"></div></div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $project->progress }}%</span>
                    </div>
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500 dark:text-gray-400">Description</dt>
                <dd class="mt-1 text-gray-700 dark:text-gray-300">{{ $project->description ?? '— No description —' }}</dd>
            </div>
        </dl>
    </div>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Stock Overview</h3>
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="rounded-xl bg-gray-50 dark:bg-gray-900 p-4">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stockStats['total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Units</p>
                </div>
                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 p-4">
                    <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $stockStats['available'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Available</p>
                </div>
                <div class="rounded-xl bg-amber-50 dark:bg-amber-900/20 p-4">
                    <p class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $stockStats['booked'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Booked</p>
                </div>
                <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/20 p-4">
                    <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">{{ $stockStats['sold'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Sold</p>
                </div>
            </div>
            <a href="{{ route('marketing-sales.project-stocks.index', ['project_id'=>$project->id]) }}" class="mt-4 block text-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">View Project Stock</a>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Quick Stats</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Leads</span><span class="font-bold text-gray-900 dark:text-white">{{ $project->leads()->count() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Closings</span><span class="font-bold text-gray-900 dark:text-white">{{ $project->closingCustomers()->count() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Stocks</span><span class="font-bold text-gray-900 dark:text-white">{{ $project->stocks()->count() }}</span></div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Units / Property Stock</h3>
        <a href="{{ route('marketing-sales.project-stocks.index', ['project_id'=>$project->id]) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Manage Stock</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Unit</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Type</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Block</th>
                    <th class="text-right px-4 py-3 font-semibold text-gray-900 dark:text-white">Price</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-900 dark:text-white">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($project->stocks()->latest()->limit(10)->get() as $stock)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $stock->unit_code }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $stock->type ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $stock->block ?? '-' }}</td>
                    <td class="px-4 py-3 text-right text-gray-900 dark:text-white">{{ $stock->price ? 'Rp '.number_format($stock->price,0,',','.') : '-' }}</td>
                    <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold bg-gray-100 dark:bg-gray-700 capitalize">{{ $stock->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No units assigned to this project yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
