@extends('layouts.app')

@section('title', 'Attendances - ' . config('app.name'))

@section('content')
<div x-data="{ importModal: false }" class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Attendances</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Export attendance data and import records from the template.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('attendances.template') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Download Template
            </a>
            <button type="button" @click="importModal = true" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
                Import Excel
            </button>
            <a href="{{ route('attendances.export', request()->query()) }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700">
                Export Excel
            </a>
        </div>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-100 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400">
        {{ $value }}
    </div>
    @endsession

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-100 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('import_errors'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
            <p class="font-semibold">Some rows were skipped:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach (array_slice(session('import_errors'), 0, 10) as $importError)
                    <li>{{ $importError }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('attendances.index') }}" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search employee, NIP, or email" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All statuses</option>
                <option value="present" @selected(request('status') === 'present')>Present</option>
                <option value="absent" @selected(request('status') === 'absent')>Absent</option>
                <option value="sick" @selected(request('status') === 'sick')>Sick</option>
                <option value="excuse" @selected(request('status') === 'excuse')>Excuse</option>
            </select>
            @if ($companies->count() > 1)
                <select name="company_id" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    <option value="">All companies</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            @endif
            <select name="department_id" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected((string) request('department_id') === (string) $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
            <select name="division_id" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All divisions</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division->id }}" @selected((string) request('division_id') === (string) $division->id)>{{ $division->name }}</option>
                @endforeach
            </select>
            <select name="section_id" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All sections</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" @selected((string) request('section_id') === (string) $section->id)>{{ $section->name }}</option>
                @endforeach
            </select>
            <select name="work_location_id" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All work locations</option>
                @foreach ($workLocations as $workLocation)
                    <option value="{{ $workLocation->id }}" @selected((string) request('work_location_id') === (string) $workLocation->id)>{{ $workLocation->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            <div class="flex gap-2">
                <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">Apply</button>
                @if (request()->hasAny(['search', 'status', 'company_id', 'department_id', 'division_id', 'section_id', 'work_location_id', 'date_from', 'date_to']))
                    <a href="{{ route('attendances.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Employee</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Clock In</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Clock Out</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Work Hours</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($attendances as $attendance)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $attendance->employee?->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->employee?->nip ?? '-' }} · {{ $attendance->employee?->department?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $attendance->clock_in?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $attendance->clock_out?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $attendance->work_hours ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $attendance->status === 'present' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($attendance->status === 'absent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                In: {{ $attendance->location_in['latitude'] ?? '-' }}, {{ $attendance->location_in['longitude'] ?? '-' }}<br>
                                Out: {{ $attendance->location_out['latitude'] ?? '-' }}, {{ $attendance->location_out['longitude'] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($attendances->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $attendances->links() }}</div>
        @endif
    </div>

    <div x-show="importModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="importModal = false"></div>
        <div class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Import Attendances</h3>
            <form action="{{ route('attendances.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <div class="flex justify-end gap-3">
                    <button type="button" @click="importModal = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
