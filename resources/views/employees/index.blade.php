@extends('layouts.app')

@section('title', 'Employees - ' . config('app.name'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Employees</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">View and manage employee records.</p>
    </div>
    <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm text-sm transition-colors">
        + Add New
    </a>
</div>

@if (session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<form method="GET" action="{{ route('employees.index') }}" class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        <input
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search name, NIP, or email"
            class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
        >

        <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
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

        <div class="flex gap-2">
            <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
                Apply
            </button>
            @if (request()->hasAny(['search', 'status', 'company_id', 'department_id', 'division_id', 'section_id', 'work_location_id']))
                <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Reset
                </a>
            @endif
        </div>
    </div>
</form>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">NIP</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Name</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Email</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Department</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Position</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Status</th>
                    <th class="text-right px-6 py-4 font-semibold text-gray-900 dark:text-white">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($employees as $employee)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ $employee->nip }}</td>
                    <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">{{ $employee->name }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $employee->email }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $employee->department?->name }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $employee->jobPosition?->name }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('employees.show', $employee) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors">
                                View
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors">
                                Edit
                            </a>
                            <button type="button" @click="open = true" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($employees->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $employees->links() }}
    </div>
    @endif
</div>
@endsection
