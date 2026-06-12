@extends('layouts.app')

@section('title', 'Tasks - ' . config('app.name'))

@section('content')
@php
    $statusClasses = [
        'pending' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
        'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
    ];
    $priorityClasses = [
        'low' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
        'normal' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
        'high' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
    ];
@endphp

<div x-data="{
    deleteModal: false,
    deleteId: null,
    completeModal: false,
    completeAction: '',
    completeTitle: '',
    evidenceError: '',
    evidenceSizeLabel: '',
    openCompleteModal(action, title) {
        this.completeAction = action;
        this.completeTitle = title;
        this.evidenceError = '';
        this.evidenceSizeLabel = '';
        this.completeModal = true;
    },
    validateEvidence(event) {
        const maxBytes = 20 * 1024 * 1024;
        const files = Array.from(event.target.files || []);
        const totalBytes = files.reduce((total, file) => total + file.size, 0);
        this.evidenceSizeLabel = files.length ? `${files.length} file(s), ${(totalBytes / 1024 / 1024).toFixed(2)} MB total` : '';
        this.evidenceError = totalBytes > maxBytes ? 'Total evidence file size must not exceed 20 MB.' : '';
        event.target.setCustomValidity(this.evidenceError);
    }
}" class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tasks</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $canAssign ? 'Create personal tasks or assign work to employees.' : 'Manage your own daily, weekly, monthly, and yearly tasks.' }}</p>
        </div>
        <a href="{{ route('employee-tasks.create') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
            Create Task
        </a>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-100 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400">
        {{ $value }}
    </div>
    @endsession

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="GET" action="{{ route('employee-tasks.index') }}" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search task, employee, or NIP" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

            @if ($canAssign)
                <select name="employee_id" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    <option value="">All employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            @endif

            <select name="period_type" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All periods</option>
                <option value="daily" @selected(request('period_type') === 'daily')>Daily</option>
                <option value="weekly" @selected(request('period_type') === 'weekly')>Weekly</option>
                <option value="monthly" @selected(request('period_type') === 'monthly')>Monthly</option>
                <option value="yearly" @selected(request('period_type') === 'yearly')>Yearly</option>
            </select>

            <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            </select>

            <select name="priority" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All priorities</option>
                <option value="low" @selected(request('priority') === 'low')>Low</option>
                <option value="normal" @selected(request('priority') === 'normal')>Normal</option>
                <option value="high" @selected(request('priority') === 'high')>High</option>
                <option value="urgent" @selected(request('priority') === 'urgent')>Urgent</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">

            <div class="flex gap-2">
                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">Apply</button>
                @if (request()->hasAny(['search', 'employee_id', 'period_type', 'status', 'priority', 'date_from', 'date_to']))
                    <a href="{{ route('employee-tasks.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Task</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Employee</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Period</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Priority</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($tasks as $task)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $task->title }}</div>
                                <div class="max-w-md truncate text-xs text-gray-500 dark:text-gray-400">{{ $task->description ?: 'No description' }}</div>
                                @if ($task->evidence_files)
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach ($task->evidence_files as $file)
                                            <a href="{{ $file['url'] ?? '#' }}" target="_blank" class="rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700 hover:text-indigo-900 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:text-indigo-200">{{ Str::limit($file['name'] ?? 'Evidence', 22) }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $task->employee?->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $task->employee?->nip ?? '-' }} · {{ $task->employee?->department?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <div class="capitalize">{{ $task->period_type }}</div>
                                <div class="text-xs">{{ $task->period_start->format('d M Y') }} - {{ $task->period_end->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $priorityClasses[$task->priority] ?? $priorityClasses['normal'] }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusClasses[$task->status] ?? $statusClasses['pending'] }}">
                                    {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                </span>
                                @if ($task->completed_at)
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $task->completed_at->format('d M Y H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex min-w-56 flex-col items-end gap-2">
                                    @if ($task->status === 'completed')
                                        <form method="POST" action="{{ route('employee-tasks.reopen', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition-colors hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                Undo Done
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" @click="openCompleteModal(@js(route('employee-tasks.complete', $task)), @js($task->title))" class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 transition-colors hover:bg-green-100 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Complete
                                        </button>
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('employee-tasks.edit', $task) }}" class="inline-flex items-center rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition-colors hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50">Edit</a>
                                        <button type="button" @click="deleteModal = true; deleteId = {{ $task->id }}" class="inline-flex items-center rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition-colors hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50">Delete</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tasks->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $tasks->links() }}</div>
        @endif
    </div>

    <div x-show="completeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="fixed inset-0 bg-gray-900/50" @click="completeModal = false"></div>
        <div class="relative flex max-h-[calc(100vh-2rem)] w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <div class="shrink-0 border-b border-gray-200 p-6 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Complete Task</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="completeTitle"></p>
            </div>
            <form method="POST" :action="completeAction" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
                @csrf
                @method('PATCH')
                <div class="min-h-0 flex-1 overflow-y-auto p-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Evidence files <span class="text-red-500">*</span></label>
                    <input type="file" name="evidence_files[]" multiple required @change="validateEvidence($event)" class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-indigo-300">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload one or more files. Total size must not exceed 20 MB.</p>
                    <p x-show="evidenceSizeLabel" x-text="evidenceSizeLabel" class="mt-2 text-xs font-medium text-gray-600 dark:text-gray-300"></p>
                    <p x-show="evidenceError" x-text="evidenceError" class="mt-2 text-xs font-medium text-red-600 dark:text-red-400"></p>
                </div>
                <div class="grid shrink-0 grid-cols-1 gap-3 border-t border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 sm:grid-cols-2">
                    <button type="button" @click="completeModal = false" class="w-full rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                    <button type="submit" :disabled="!!evidenceError" class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500 dark:disabled:bg-gray-700 dark:disabled:text-gray-400">Complete Task</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Are you sure you want to delete this task?</p>
            <form :action="`/employee-tasks/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
