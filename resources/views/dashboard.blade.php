@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- Welcome --}}
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        Welcome back, {{ Auth::user()->name ?? 'User' }}
    </h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Here is what is happening with your organization today.
    </p>
</div>

@if ($errors->any())
<div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
    {{ $errors->first() }}
</div>
@endif

{{-- Metric Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
    {{-- Total Employees --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2.5 py-1 rounded-full">{{ number_format($metrics['activeEmployees']) }} active</span>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['totalEmployees']) }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Total Employees</p>
    </div>

    {{-- Active Today --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-2.5 py-1 rounded-full">Today</span>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['activeToday']) }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Active Today</p>
    </div>

    {{-- Departments --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-full">Master data</span>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['departments']) }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Departments</p>
    </div>

    {{-- Expiring Contracts --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 px-2.5 py-1 rounded-full">Next 30 days</span>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($metrics['expiringContracts']) }}</p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Expiring Contracts</p>
    </div>
</div>

{{-- Tasks --}}
<div x-data="{
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
    }" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Task List</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $canManageDashboardTasks ? 'Latest employee tasks across the team' : 'Your latest tasks' }}</p>
        </div>
        <a href="{{ route('employee-tasks.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">Add Task</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @forelse ($dashboardTasks as $task)
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 transition-colors dark:border-gray-700 dark:bg-gray-900/50">
            <div class="flex items-start gap-3">
                @if ($task->status === 'completed')
                <form method="POST" action="{{ route('employee-tasks.reopen', $task) }}" class="shrink-0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-700 ring-1 ring-green-200 transition-colors hover:bg-amber-100 hover:text-amber-700 hover:ring-amber-200 dark:bg-green-900/30 dark:text-green-300 dark:ring-green-800 dark:hover:bg-amber-900/30 dark:hover:text-amber-300 dark:hover:ring-amber-800" title="Undo done">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </form>
                @else
                <button type="button" @click="openCompleteModal(@js(route('employee-tasks.complete', $task)), @js($task->title))" class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-400 transition-colors hover:border-green-500 hover:bg-green-50 hover:text-green-600 dark:border-gray-600 dark:bg-gray-800 dark:hover:border-green-500 dark:hover:bg-green-900/20 dark:hover:text-green-300" title="Mark task as done">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white {{ $task->status === 'completed' ? 'line-through decoration-2 opacity-60' : '' }}">{{ $task->title }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($task->period_type) }} · {{ $task->period_start->format('d M') }} - {{ $task->period_end->format('d M Y') }}</p>
                    <p class="mt-2 truncate text-xs text-gray-600 dark:text-gray-300">{{ $task->employee?->name ?? 'Unassigned' }}{{ $task->employee?->department ? ' · '.$task->employee->department->name : '' }}</p>
                    @if ($task->status === 'completed')
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @forelse ($task->evidence_files ?? [] as $file)
                        <a href="{{ $file['url'] ?? '#' }}" target="_blank" class="rounded-full bg-white px-2 py-0.5 text-[11px] font-medium text-indigo-600 hover:text-indigo-800 dark:bg-gray-800 dark:text-indigo-300 dark:hover:text-indigo-200">{{ Str::limit($file['name'] ?? 'Evidence', 18) }}</a>
                        @empty
                        <span class="text-[11px] text-gray-400 dark:text-gray-500">No evidence files</span>
                        @endforelse
                    </div>
                    @else
                    <div class="mt-3 inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                        Evidence required to complete
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 xl:col-span-4 rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            No tasks yet.
        </div>
        @endforelse
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
                    <button type="submit" :disabled="!!evidenceError" class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-green-600 transition-colors hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500 dark:disabled:bg-gray-700 dark:disabled:text-gray-400">Complete Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Employees by Department</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Active employees grouped by department</p>
            </div>
        </div>
        <div class="h-72">
            <canvas id="departmentEmployeesChart" class="h-full w-full"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attendance Status</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Current month attendance records</p>
        </div>
        <div class="h-72">
            <canvas id="attendanceStatusChart" class="h-full w-full"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Employee Status</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total employees by status</p>
        </div>
        <div class="h-64">
            <canvas id="employeeStatusChart" class="h-full w-full"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Leave Balance</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Used, remaining, and extra leave days</p>
        </div>
        <div class="h-64">
            <canvas id="leaveBalanceChart" class="h-full w-full"></canvas>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Work Locations</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Employees grouped by assigned location</p>
        </div>
        <div class="h-64">
            <canvas id="workLocationsChart" class="h-full w-full"></canvas>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Employee Trend</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Employees joined over the last 6 months</p>
        </div>
        <a href="{{ route('employees.create') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors shrink-0">Add Employee</a>
    </div>
    <div class="h-64">
        <canvas id="monthlyJoinsChart" class="h-full w-full"></canvas>
    </div>
</div>

{{-- Recent Activity --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Employees</h3>
        <a href="{{ route('employees.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors shrink-0">View All</a>
    </div>
    <div class="space-y-4">
        @forelse ($recentEmployees as $employee)
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
            <span class="w-2 h-2 bg-indigo-400 rounded-full shrink-0"></span>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900 dark:text-white">
                    <span class="font-medium">{{ $employee->name }}</span>
                    was added as
                    <span class="font-medium">{{ $employee->jobPosition?->name ?? 'Unassigned Position' }}</span>
                    in {{ $employee->department?->name ?? 'Unassigned Department' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $employee->created_at->diffForHumans() }}</p>
            </div>
            <a href="{{ route('employees.show', $employee) }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 shrink-0 sm:self-center">Details</a>
        </div>
        @empty
        <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
            No employees have been added yet.
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const dashboardCharts = @json($charts);
        const palette = ['#4f46e5', '#16a34a', '#f59e0b', '#9333ea', '#0ea5e9', '#ef4444', '#14b8a6', '#64748b'];

        const isDarkMode = () => document.documentElement.classList.contains('dark');

        const chartTheme = () => ({
            text: isDarkMode() ? '#d1d5db' : '#374151',
            muted: isDarkMode() ? '#6b7280' : '#9ca3af',
            grid: isDarkMode() ? '#374151' : '#e5e7eb',
            empty: isDarkMode() ? '#9ca3af' : '#6b7280',
        });

        const setupCanvas = (canvas) => {
            const rect = canvas.getBoundingClientRect();
            const ratio = window.devicePixelRatio || 1;
            canvas.width = Math.max(rect.width * ratio, 1);
            canvas.height = Math.max(rect.height * ratio, 1);

            const context = canvas.getContext('2d');
            context.setTransform(ratio, 0, 0, ratio, 0, 0);
            context.clearRect(0, 0, rect.width, rect.height);

            return {
                context,
                width: rect.width,
                height: rect.height
            };
        };

        const hasValues = (data) => (data.values || []).some((value) => Number(value) > 0);

        const roundedRect = (context, x, y, width, height, radius) => {
            const safeRadius = Math.min(radius, Math.abs(width) / 2, Math.abs(height) / 2);

            if (typeof context.roundRect === 'function') {
                context.roundRect(x, y, width, height, safeRadius);
                return;
            }

            context.moveTo(x + safeRadius, y);
            context.lineTo(x + width - safeRadius, y);
            context.quadraticCurveTo(x + width, y, x + width, y + safeRadius);
            context.lineTo(x + width, y + height - safeRadius);
            context.quadraticCurveTo(x + width, y + height, x + width - safeRadius, y + height);
            context.lineTo(x + safeRadius, y + height);
            context.quadraticCurveTo(x, y + height, x, y + height - safeRadius);
            context.lineTo(x, y + safeRadius);
            context.quadraticCurveTo(x, y, x + safeRadius, y);
        };

        const drawEmpty = (context, width, height, label) => {
            const theme = chartTheme();
            context.fillStyle = theme.empty;
            context.font = '500 14px Instrument Sans, sans-serif';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText(label, width / 2, height / 2);
        };

        const drawLegend = (context, labels, colors, left, top) => {
            const theme = chartTheme();
            context.font = '500 12px Instrument Sans, sans-serif';
            context.textAlign = 'left';
            context.textBaseline = 'middle';

            labels.forEach((label, index) => {
                const y = top + (index * 22);
                context.fillStyle = colors[index % colors.length];
                context.beginPath();
                roundedRect(context, left, y - 5, 10, 10, 3);
                context.fill();
                context.fillStyle = theme.text;
                context.fillText(label, left + 16, y);
            });
        };

        const drawBarChart = (id, data, options = {}) => {
            const canvas = document.getElementById(id);
            if (!canvas) return;

            const {
                context,
                width,
                height
            } = setupCanvas(canvas);
            if (!hasValues(data)) {
                drawEmpty(context, width, height, options.emptyLabel || 'No data available yet');
                return;
            }

            const theme = chartTheme();
            const labels = data.labels || [];
            const values = (data.values || []).map(Number);
            const maxValue = Math.max(...values, 1);
            const padding = {
                top: 16,
                right: 16,
                bottom: 52,
                left: 42
            };
            const chartWidth = width - padding.left - padding.right;
            const chartHeight = height - padding.top - padding.bottom;
            const gap = 14;
            const barWidth = Math.max((chartWidth - (gap * Math.max(values.length - 1, 0))) / Math.max(values.length, 1), 10);

            context.strokeStyle = theme.grid;
            context.lineWidth = 1;
            context.font = '500 11px Instrument Sans, sans-serif';
            context.fillStyle = theme.muted;
            context.textAlign = 'right';
            context.textBaseline = 'middle';

            [0, 0.25, 0.5, 0.75, 1].forEach((step) => {
                const y = padding.top + chartHeight - (chartHeight * step);
                context.beginPath();
                context.moveTo(padding.left, y);
                context.lineTo(width - padding.right, y);
                context.stroke();
                context.fillText(Math.round(maxValue * step), padding.left - 8, y);
            });

            values.forEach((value, index) => {
                const x = padding.left + (index * (barWidth + gap));
                const barHeight = (value / maxValue) * chartHeight;
                const y = padding.top + chartHeight - barHeight;

                context.fillStyle = palette[index % palette.length];
                context.beginPath();
                roundedRect(context, x, y, barWidth, barHeight, 6);
                context.fill();

                context.fillStyle = theme.text;
                context.textAlign = 'center';
                context.textBaseline = 'top';
                context.font = '600 12px Instrument Sans, sans-serif';
                context.fillText(value, x + (barWidth / 2), Math.max(y - 18, 4));

                context.save();
                context.translate(x + (barWidth / 2), height - 28);
                context.rotate(-Math.PI / 5);
                context.textAlign = 'right';
                context.font = '500 11px Instrument Sans, sans-serif';
                context.fillText(String(labels[index] || '').slice(0, 16), 0, 0);
                context.restore();
            });
        };

        const drawDonutChart = (id, data, options = {}) => {
            const canvas = document.getElementById(id);
            if (!canvas) return;

            const {
                context,
                width,
                height
            } = setupCanvas(canvas);
            if (!hasValues(data)) {
                drawEmpty(context, width, height, options.emptyLabel || 'No data available yet');
                return;
            }

            const labels = data.labels || [];
            const values = (data.values || []).map(Number);
            const total = values.reduce((sum, value) => sum + value, 0);
            const colors = labels.map((_, index) => palette[index % palette.length]);
            const compact = width < 380;
            const radius = Math.min(width, height) * (compact ? 0.22 : 0.28);
            const centerX = compact ? width / 2 : Math.min(width * 0.38, width / 2);
            const centerY = compact ? height * 0.38 : height / 2;
            let start = -Math.PI / 2;

            values.forEach((value, index) => {
                const slice = (value / total) * Math.PI * 2;
                context.beginPath();
                context.arc(centerX, centerY, radius, start, start + slice);
                context.lineWidth = Math.max(radius * 0.42, 18);
                context.strokeStyle = colors[index];
                context.stroke();
                start += slice;
            });

            const theme = chartTheme();
            context.fillStyle = theme.text;
            context.font = '700 20px Instrument Sans, sans-serif';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText(total.toLocaleString(), centerX, centerY - 4);
            context.font = '500 11px Instrument Sans, sans-serif';
            context.fillStyle = theme.muted;
            context.fillText(options.centerLabel || 'Total', centerX, centerY + 18);

            drawLegend(
                context,
                labels.map((label, index) => `${label}: ${values[index].toLocaleString()}`),
                colors,
                compact ? 20 : Math.min(width * 0.66, centerX + radius + 28),
                compact ? height - Math.max(labels.length * 22, 44) : Math.max(24, centerY - ((labels.length - 1) * 11))
            );
        };

        const drawLineChart = (id, data) => {
            const canvas = document.getElementById(id);
            if (!canvas) return;

            const {
                context,
                width,
                height
            } = setupCanvas(canvas);
            const labels = data.labels || [];
            const values = (data.values || []).map(Number);
            const theme = chartTheme();
            const maxValue = Math.max(...values, 1);
            const padding = {
                top: 20,
                right: 28,
                bottom: 36,
                left: 42
            };
            const chartWidth = width - padding.left - padding.right;
            const chartHeight = height - padding.top - padding.bottom;

            context.strokeStyle = theme.grid;
            context.lineWidth = 1;
            [0, 0.25, 0.5, 0.75, 1].forEach((step) => {
                const y = padding.top + chartHeight - (chartHeight * step);
                context.beginPath();
                context.moveTo(padding.left, y);
                context.lineTo(width - padding.right, y);
                context.stroke();
            });

            const points = values.map((value, index) => ({
                x: padding.left + ((chartWidth / Math.max(values.length - 1, 1)) * index),
                y: padding.top + chartHeight - ((value / maxValue) * chartHeight),
                value,
            }));

            context.beginPath();
            points.forEach((point, index) => {
                if (index === 0) {
                    context.moveTo(point.x, point.y);
                } else {
                    context.lineTo(point.x, point.y);
                }
            });
            context.strokeStyle = '#4f46e5';
            context.lineWidth = 3;
            context.stroke();

            points.forEach((point, index) => {
                context.fillStyle = '#4f46e5';
                context.beginPath();
                context.arc(point.x, point.y, 4, 0, Math.PI * 2);
                context.fill();

                context.fillStyle = theme.text;
                context.font = '600 12px Instrument Sans, sans-serif';
                context.textAlign = 'center';
                context.fillText(point.value, point.x, point.y - 12);

                context.fillStyle = theme.muted;
                context.font = '500 12px Instrument Sans, sans-serif';
                context.fillText(labels[index], point.x, height - 12);
            });
        };

        const renderCharts = () => {
            drawBarChart('departmentEmployeesChart', dashboardCharts.departmentEmployees);
            drawDonutChart('attendanceStatusChart', dashboardCharts.attendanceStatus, {
                centerLabel: 'Records'
            });
            drawDonutChart('employeeStatusChart', dashboardCharts.employeeStatus, {
                centerLabel: 'Employees'
            });
            drawDonutChart('leaveBalanceChart', dashboardCharts.leaveBalance, {
                centerLabel: 'Days'
            });
            drawBarChart('workLocationsChart', dashboardCharts.workLocations);
            drawLineChart('monthlyJoinsChart', dashboardCharts.monthlyJoins);
        };

        window.addEventListener('resize', renderCharts);

        new MutationObserver(renderCharts).observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });

        renderCharts();
    })();
</script>
@endpush