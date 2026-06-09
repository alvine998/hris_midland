@extends('layouts.app')

@section('title', 'Leave Requests - ' . config('app.name'))

@section('content')
<div x-data="{ createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null }" class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Leave Requests</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage employee leave requests and create requests in bulk.</p>
        </div>
        <button type="button" @click="createModal = true; editModal = false; editItem = { status: 'on_progress', inclusive_days: 1 }" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
            Bulk Create
        </button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-100 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400">
        {{ $value }}
    </div>
    @endsession

    <form method="GET" action="{{ route('leave-requests.index') }}" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search employee, NIP, email, title, reason" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
            <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All statuses</option>
                <option value="on_progress" @selected(request('status') === 'on_progress')>On Progress</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
            </select>
            <select name="leave_type_id" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                <option value="">All leave types</option>
                @foreach ($leaveTypes as $leaveType)
                    <option value="{{ $leaveType->id }}" @selected((string) request('leave_type_id') === (string) $leaveType->id)>{{ $leaveType->name }}</option>
                @endforeach
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
                @if (request()->hasAny(['search', 'status', 'leave_type_id', 'company_id', 'department_id', 'division_id', 'section_id', 'work_location_id', 'date_from', 'date_to']))
                    <a href="{{ route('leave-requests.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Reset</a>
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
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Title</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Type</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Dates</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Days</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($leaveRequests as $leaveRequest)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $leaveRequest->employee?->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $leaveRequest->employee?->nip ?? '-' }} · {{ $leaveRequest->employee?->department?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $leaveRequest->title }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $leaveRequest->leaveType?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $leaveRequest->start_date->format('d M Y') }} - {{ $leaveRequest->end_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $leaveRequest->inclusive_days }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $leaveRequest->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($leaveRequest->status === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400') }}">
                                    {{ str_replace('_', ' ', ucfirst($leaveRequest->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="editModal = true; editItem = { id: {{ $leaveRequest->id }}, employee_id: @js($leaveRequest->employee_id), leave_type_id: @js($leaveRequest->leave_type_id), employee_delegation_id: @js($leaveRequest->employee_delegation_id), title: @js($leaveRequest->title), start_date: @js($leaveRequest->start_date->format('Y-m-d')), end_date: @js($leaveRequest->end_date->format('Y-m-d')), inclusive_days: @js($leaveRequest->inclusive_days), evidence: @js($leaveRequest->evidence ?? ''), reason: @js($leaveRequest->reason ?? ''), notes: @js($leaveRequest->notes ?? ''), status: @js($leaveRequest->status) }" class="mr-3 text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                <button type="button" @click="deleteModal = true; deleteId = {{ $leaveRequest->id }}" class="text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No leave requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($leaveRequests->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $leaveRequests->links() }}</div>
        @endif
    </div>

    <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
        <div class="relative max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="editModal ? 'Edit Leave Request' : 'Bulk Create Leave Requests'"></h3>
            <form :action="editModal ? `/leave-requests/${editItem.id}` : '{{ route('leave-requests.bulk-store') }}'" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <template x-if="editModal">
                        <select name="employee_id" x-model="editItem.employee_id" required class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select employee...</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->nip ?? 'No NIP' }})</option>
                            @endforeach
                        </select>
                    </template>
                    <template x-if="! editModal">
                        <select name="employee_ids[]" multiple required class="min-h-44 rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->nip ?? 'No NIP' }})</option>
                            @endforeach
                        </select>
                    </template>
                    <select name="leave_type_id" x-model="editItem.leave_type_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Leave type...</option>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="title" x-model="editItem.title" required placeholder="Title" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <select name="employee_delegation_id" x-model="editItem.employee_delegation_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Delegated employee...</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="start_date" x-model="editItem.start_date" required class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <input type="date" name="end_date" x-model="editItem.end_date" required class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <input type="number" name="inclusive_days" x-model="editItem.inclusive_days" min="0" required placeholder="Inclusive days" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <select name="status" x-model="editItem.status" required class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="on_progress">On Progress</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="text" name="evidence" x-model="editItem.evidence" placeholder="Evidence file path" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <textarea name="reason" x-model="editItem.reason" rows="3" placeholder="Reason" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white md:col-span-2"></textarea>
                    <textarea name="notes" x-model="editItem.notes" rows="3" placeholder="Notes" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white md:col-span-2"></textarea>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="createModal = false; editModal = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Create Requests'"></button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            <form :action="`/leave-requests/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
