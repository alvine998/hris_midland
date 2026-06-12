@extends('layouts.app')

@section('title', 'Leave Settings - ' . config('app.name'))

@section('content')
<div x-data="{ showModal: false, editMode: false, editItem: null, deleteModal: false, deleteId: null, form: { company_id: '', is_advance_leave: false, max_advance_leave: 0, is_rollover: false, max_rollover: 0 } }" class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Leave Settings</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Configure leave policies per company.</p>
        </div>
        <button type="button" @click="editMode = false; editItem = null; form = { company_id: '', is_advance_leave: false, max_advance_leave: 0, is_rollover: false, max_rollover: 0 }; showModal = true" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
            Add Setting
        </button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-100 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400">
        {{ $value }}
    </div>
    @endsession

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Company</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Advance Leave</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Max Advance</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Rollover</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Max Rollover</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($leaveSettings as $setting)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $setting->company?->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($setting->is_advance_leave)
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Enabled</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">Disabled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $setting->max_advance_leave ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($setting->is_rollover)
                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Enabled</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">Disabled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $setting->max_rollover ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="editMode = true; editItem = {{ $setting->id }}; form = { company_id: '{{ $setting->company_id }}', is_advance_leave: {{ $setting->is_advance_leave ? 'true' : 'false' }}, max_advance_leave: {{ $setting->max_advance_leave ?? 0 }}, is_rollover: {{ $setting->is_rollover ? 'true' : 'false' }}, max_rollover: {{ $setting->max_rollover ?? 0 }} }; showModal = true" class="mr-3 text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                <button type="button" @click="deleteModal = true; deleteId = {{ $setting->id }}" class="text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No leave settings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="showModal = false"></div>
        <div class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Leave Setting' : 'Add Leave Setting'"></h3>
            <form :action="editMode ? `/leave-settings/${editItem}` : '{{ route('leave-settings.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Company</label>
                    <select name="company_id" x-model="form.company_id" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="">Select company...</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_advance_leave" value="1" x-model="form.is_advance_leave" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Allow Advance Leave</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Max Advance Leave (days)</label>
                    <input type="number" name="max_advance_leave" x-model="form.max_advance_leave" min="0" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white" placeholder="0">
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_rollover" value="1" x-model="form.is_rollover" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Allow Rollover</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Max Rollover (days)</label>
                    <input type="number" name="max_rollover" x-model="form.max_rollover" min="0" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white" placeholder="0">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700" x-text="editMode ? 'Update' : 'Create'"></button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            <form :action="`/leave-settings/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
