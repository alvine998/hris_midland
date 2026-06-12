@extends('layouts.app')

@section('title', 'Edit Leave Request - ' . config('app.name'))

@section('content')
<div x-data="{ employeeId: @js(old('employee_id', $leaveRequest->employee_id)), delegationId: @js(old('employee_delegation_id', $leaveRequest->employee_delegation_id)) }">
    <div class="mb-6">
        <a href="{{ route('leave-requests.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">&larr; Back to Leave Requests</a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Edit Leave Request</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update the leave request details below.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Employee <span class="text-red-500">*</span></label>
                    <x-employee-async-select
                        name="employee_id"
                        model="employeeId"
                        placeholder="Search employee by name, NIP, or email..."
                        required
                    />
                    @error('employee_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Leave Type</label>
                    <select name="leave_type_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm">
                        <option value="">Select leave type...</option>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}" @selected(old('leave_type_id', $leaveRequest->leave_type_id) == $leaveType->id)>{{ $leaveType->name }}</option>
                        @endforeach
                    </select>
                    @error('leave_type_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $leaveRequest->title) }}" required placeholder="Leave request title" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('title') border-red-500 @enderror">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Delegated Employee</label>
                    <x-employee-async-select
                        name="employee_delegation_id"
                        model="delegationId"
                        placeholder="Search delegated employee..."
                    />
                    @error('employee_delegation_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $leaveRequest->start_date->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('start_date') border-red-500 @enderror">
                    @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">End Date <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $leaveRequest->end_date->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('end_date') border-red-500 @enderror">
                    @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Inclusive Days</label>
                    <input type="number" name="inclusive_days" value="{{ old('inclusive_days', $leaveRequest->inclusive_days) }}" min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('inclusive_days') border-red-500 @enderror" placeholder="Number of days">
                    @error('inclusive_days')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('status') border-red-500 @enderror">
                        <option value="on_progress" @selected(old('status', $leaveRequest->status) === 'on_progress')>On Progress</option>
                        <option value="approved" @selected(old('status', $leaveRequest->status) === 'approved')>Approved</option>
                        <option value="rejected" @selected(old('status', $leaveRequest->status) === 'rejected')>Rejected</option>
                        <option value="cancelled" @selected(old('status', $leaveRequest->status) === 'cancelled')>Cancelled</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Evidence</label>
                    <input type="text" name="evidence" value="{{ old('evidence', $leaveRequest->evidence) }}" placeholder="Evidence file path" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('evidence') border-red-500 @enderror">
                    @error('evidence')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Reason</label>
                    <textarea name="reason" rows="3" placeholder="Reason for leave" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('reason') border-red-500 @enderror">{{ old('reason', $leaveRequest->reason) }}</textarea>
                    @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Additional notes" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 text-sm @error('notes') border-red-500 @enderror">{{ old('notes', $leaveRequest->notes) }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('leave-requests.index') }}" class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">Update Leave Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
