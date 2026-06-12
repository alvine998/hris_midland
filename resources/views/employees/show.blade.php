@extends('layouts.app')

@section('title', $employee->name . ' - ' . config('app.name'))

@section('content')
<div class="mb-6 flex items-start justify-between">
    <div>
        <a href="{{ route('employees.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">&larr; Back to Employees</a>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $employee->name }}</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Employee details and records.</p>
    </div>
    <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm text-sm transition-colors">Edit Employee</a>
</div>

@if (session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div
    x-data="{ activeTab: 'information' }"
    class="space-y-6 min-w-0 max-w-full">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm max-w-full overflow-hidden">
        <div class="max-w-full overflow-x-auto overscroll-x-contain">
            <div class="flex w-max gap-1 px-4 py-3">
                <button type="button" @click="activeTab = 'information'" :class="activeTab === 'information' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Information</button>
                <button type="button" @click="activeTab = 'contracts'" :class="activeTab === 'contracts' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Contracts</button>
                <button type="button" @click="activeTab = 'family'" :class="activeTab === 'family' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Family</button>
                <button type="button" @click="activeTab = 'emergency_contacts'" :class="activeTab === 'emergency_contacts' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Emergency Contacts</button>
                <button type="button" @click="activeTab = 'education'" :class="activeTab === 'education' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Education</button>
                <button type="button" @click="activeTab = 'work_history'" :class="activeTab === 'work_history' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Work History</button>
                <button type="button" @click="activeTab = 'attendances'" :class="activeTab === 'attendances' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Attendances</button>
                <button type="button" @click="activeTab = 'leave_balance'" :class="activeTab === 'leave_balance' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Leave Balance</button>
                <button type="button" @click="activeTab = 'leave_requests'" :class="activeTab === 'leave_requests' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Leave Requests</button>
                <button type="button" @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Documents</button>
                <button type="button" @click="activeTab = 'shifts'" :class="activeTab === 'shifts' ? 'bg-indigo-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'" class="shrink-0 px-4 py-2 text-sm font-medium rounded-xl transition-colors">Shifts</button>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'information'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                {{-- Identity Card --}}
                @include('employees.partials._identity-card')
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm mt-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Info</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">NIP</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->nip ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">NIK</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->nik ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">NPWP</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->npwp ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->phone }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Birth Place</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->birth_place ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Birth Date</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->birth_date ? $employee->birth_date->format('d M Y') : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Marital Status</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ ucfirst($employee->marital_status) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Religion</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->religion?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Join Date</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ $employee->join_date ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Organization & Position</h3>
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Company</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->company?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Department</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->department?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Division</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->division?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Section</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->section?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Position</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->jobPosition?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Work Location</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->workLocation?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                @if ($employee->salary)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Salary</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Basic Salary</p>
                            <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($employee->salary->basic_salary, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Allowance</p>
                            <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($employee->salary->allowance, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">BPJS Kes</p>
                            <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($employee->salary->bpjs_kes, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">BPJS TK</p>
                            <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($employee->salary->bpjs_tk, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Tax Status</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->salary->tax_status }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Bank</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->salary->bank_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Account No</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->salary->bank_account_number ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 mb-1">Account Name</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $employee->salary->bank_account_name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Contracts Section --}}
        <div x-data="{
    createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null
}" x-show="activeTab === 'contracts'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contracts</h3>
                <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Contract No</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Name</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Type</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Start Date</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">End Date</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->contracts as $contract)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ $contract->contract_number }}</td>
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $contract->name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $contract->contractType?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $contract->start_date ? $contract->start_date->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $contract->end_date ? $contract->end_date->format('d M Y') : 'Ongoing' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $contract->id }}, contract_number: @js($contract->contract_number), name: @js($contract->name), contract_type_id: {{ $contract->contract_type_id }}, start_date: @js($contract->start_date ? $contract->start_date->format('Y-m-d') : ''), end_date: @js($contract->end_date ? $contract->end_date->format('Y-m-d') : ''), files: @js($contract->files ?? '') }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $contract->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No contracts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Contract Create/Edit Modal --}}
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="createModal = false; editModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Contract' : 'Add Contract'"></h3>
                    <form :action="editModal ? `/contracts/${editItem.id}` : '/contracts'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contract Number</label>
                                <input type="text" name="contract_number" x-model="editItem.contract_number" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="CTR-001">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <input type="text" name="name" x-model="editItem.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Contract name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                <select name="contract_type_id" x-model="editItem.contract_type_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select...</option>
                                    @foreach($contractTypes as $ct)
                                    <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                    <input type="date" name="start_date" x-model="editItem.start_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                    <input type="date" name="end_date" x-model="editItem.end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Contract Delete Modal --}}
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this contract? This action cannot be undone.</p>
                    <form :action="`/contracts/${deleteId}`" method="POST" class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Education History Section --}}
        <div x-data="{
    createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null
}" x-show="activeTab === 'education'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Education History</h3>
                <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Level</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Degree</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Major</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Start Year</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">End Year</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Notes</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->educationHistories as $education)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $education->educationType?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $education->degree ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $education->major ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $education->start_year }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $education->end_year }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $education->notes ?? '-' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $education->id }}, education_type_id: {{ $education->education_type_id }}, start_year: @js($education->start_year), end_year: @js($education->end_year), major: @js($education->major ?? ''), degree: @js($education->degree ?? ''), notes: @js($education->notes ?? '') }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $education->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No education records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Education Create/Edit Modal --}}
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="createModal = false; editModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Education' : 'Add Education'"></h3>
                    <form :action="editModal ? `/education-histories/${editItem.id}` : '/education-histories'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level</label>
                                <select name="education_type_id" x-model="editItem.education_type_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select...</option>
                                    @foreach($educationTypes as $et)
                                    <option value="{{ $et->id }}">{{ $et->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Degree</label>
                                    <input type="text" name="degree" x-model="editItem.degree" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="S1, D3, SMA">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Major</label>
                                    <input type="text" name="major" x-model="editItem.major" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Major">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Year</label>
                                    <input type="text" name="start_year" x-model="editItem.start_year" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 2018">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Year</label>
                                    <input type="text" name="end_year" x-model="editItem.end_year" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 2022">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                                <textarea name="notes" x-model="editItem.notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Education notes"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Education Delete Modal --}}
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this education record? This action cannot be undone.</p>
                    <form :action="`/education-histories/${deleteId}`" method="POST" class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Family Section --}}
        <div x-data="{
    createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null
}" x-show="activeTab === 'family'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Family</h3>
                <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Name</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Relation</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Phone</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->families as $family)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $family->name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $family->relationship?->name ?? $family->familyType?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $family->phone ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $family->status === 'hidup' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400' }}">
                                    {{ ucfirst($family->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $family->id }}, name: @js($family->name), relationship_id: @js($family->relationship_id), family_type_id: @js($family->family_type_id), phone: @js($family->phone ?? ''), status: @js($family->status) }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $family->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No family records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Family Create/Edit Modal --}}
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="createModal = false; editModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Family Member' : 'Add Family Member'"></h3>
                    <form :action="editModal ? `/families/${editItem.id}` : '/families'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <input type="text" name="name" x-model="editItem.name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Family member name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Relationship</label>
                                <select name="relationship_id" x-model="editItem.relationship_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select...</option>
                                    @foreach($relationships as $relationship)
                                    <option value="{{ $relationship->id }}">{{ $relationship->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                <input type="text" name="phone" x-model="editItem.phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Phone number">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select name="status" x-model="editItem.status" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    <option value="hidup">Hidup</option>
                                    <option value="wafat">Wafat</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Family Delete Modal --}}
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this family member? This action cannot be undone.</p>
                    <form :action="`/families/${deleteId}`" method="POST" class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Emergency Contacts Section --}}
        <div x-data="{
    createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null
}" x-show="activeTab === 'emergency_contacts'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Emergency Contacts</h3>
                <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Name</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Relationship</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Phone</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Email</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Address</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->emergencyContacts as $contact)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $contact->name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $contact->relationship?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $contact->phone }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $contact->email ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $contact->address ?? '-' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $contact->id }}, name: @js($contact->name), relationship_id: @js($contact->relationship_id), phone: @js($contact->phone), email: @js($contact->email ?? ''), address: @js($contact->address ?? '') }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $contact->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No emergency contacts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Emergency Contact' : 'Add Emergency Contact'"></h3>
                    <form :action="editModal ? `/emergency-contacts/${editItem.id}` : '/emergency-contacts'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="space-y-4">
                            <input type="text" name="name" x-model="editItem.name" required placeholder="Name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <select name="relationship_id" x-model="editItem.relationship_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select relationship...</option>
                                @foreach($relationships as $relationship)
                                <option value="{{ $relationship->id }}">{{ $relationship->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="phone" x-model="editItem.phone" required placeholder="Phone" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <input type="email" name="email" x-model="editItem.email" placeholder="Email" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <input type="text" name="address" x-model="editItem.address" placeholder="Address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button>
                        </div>
                    </form>
                </div>
            </div>
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this emergency contact?</p>
                    <form :action="`/emergency-contacts/${deleteId}`" method="POST" class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Work History Section --}}
        <div x-data="{
    createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null
}" x-show="activeTab === 'work_history'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Work History</h3>
                <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Company</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Position</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Start</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">End</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Description</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">File</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->workHistories as $work)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $work->company_name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $work->position }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $work->start_date ? $work->start_date->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $work->end_date ? $work->end_date->format('d M Y') : 'Present' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ Str::limit($work->description, 50) ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $work->file ?? '-' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $work->id }}, company_name: @js($work->company_name), position: @js($work->position), description: @js($work->description ?? ''), file: @js($work->file ?? ''), start_date: @js($work->start_date ? $work->start_date->format('Y-m-d') : ''), end_date: @js($work->end_date ? $work->end_date->format('Y-m-d') : '') }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $work->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No work history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Work History Create/Edit Modal --}}
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="createModal = false; editModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Work History' : 'Add Work History'"></h3>
                    <form :action="editModal ? `/work-histories/${editItem.id}` : '/work-histories'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                                <input type="text" name="company_name" x-model="editItem.company_name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Company name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Position</label>
                                <input type="text" name="position" x-model="editItem.position" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Job position">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                                    <input type="date" name="start_date" x-model="editItem.start_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                    <input type="date" name="end_date" x-model="editItem.end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea name="description" x-model="editItem.description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Job description"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File</label>
                                <input type="text" name="file" x-model="editItem.file" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="File name or path">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Work History Delete Modal --}}
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModal = false">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this work history? This action cannot be undone.</p>
                    <form :action="`/work-histories/${deleteId}`" method="POST" class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Documents Section --}}
        <div x-data="{ createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null }" x-show="activeTab === 'documents'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Documents</h3>
                <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Name</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Type</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Expired At</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">File</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->documents as $document)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $document->name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $document->documentType?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $document->expired_at ? $document->expired_at->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $document->file ?? '-' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $document->id }}, name: @js($document->name), document_type_id: @js($document->document_type_id), description: @js($document->description ?? ''), file: @js($document->file ?? ''), expired_at: @js($document->expired_at ? $document->expired_at->format('Y-m-d') : '') }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $document->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No documents found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Document' : 'Add Document'"></h3>
                    <form :action="editModal ? `/documents/${editItem.id}` : '/documents'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="space-y-4">
                            <input type="text" name="name" x-model="editItem.name" required placeholder="Document name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <select name="document_type_id" x-model="editItem.document_type_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select type...</option>
                                @foreach($documentTypes as $documentType)
                                <option value="{{ $documentType->id }}">{{ $documentType->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="file" x-model="editItem.file" placeholder="File path or URL" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <input type="date" name="expired_at" x-model="editItem.expired_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <textarea name="description" x-model="editItem.description" rows="3" placeholder="Description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button>
                        </div>
                    </form>
                </div>
            </div>
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <form :action="`/documents/${deleteId}`" method="POST" class="flex justify-center gap-3 mt-6">@csrf @method('DELETE')<button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button><button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button></form>
                </div>
            </div>
        </div>

        {{-- Attendance Section --}}
        <div x-show="activeTab === 'attendances'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Attendance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Clock In</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Clock Out</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Hours</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->attendances as $attendance)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $attendance->clock_in ? $attendance->clock_in->format('d M Y H:i') : '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $attendance->clock_out ? $attendance->clock_out->format('d M Y H:i') : '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $attendance->work_hours ?? '-' }}h</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @switch($attendance->status)
                                @case('present') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 @break
                                @case('absent') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 @break
                                @case('sick') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 @break
                                @case('excuse') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 @break
                                @default bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400
                            @endswitch
                        ">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No attendance records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Employee Shifts Section --}}
        <div x-data="{ createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null }" x-show="activeTab === 'shifts'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Employee Shifts</h3>
                <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Shift</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Start Date</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">End Date</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->employeeShifts as $employeeShift)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $employeeShift->shift?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $employeeShift->start_date->format('d M Y') }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $employeeShift->end_date ? $employeeShift->end_date->format('d M Y') : 'Ongoing' }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $employeeShift->id }}, shift_id: @js($employeeShift->shift_id), start_date: @js($employeeShift->start_date->format('Y-m-d')), end_date: @js($employeeShift->end_date ? $employeeShift->end_date->format('Y-m-d') : '') }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $employeeShift->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No shift assignments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Shift Assignment' : 'Add Shift Assignment'"></h3>
                    <form :action="editModal ? `/employee-shifts/${editItem.id}` : '/employee-shifts'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="space-y-4">
                            <select name="shift_id" x-model="editItem.shift_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">Select shift...</option>@foreach($shifts as $shift)<option value="{{ $shift->id }}">{{ $shift->name }}</option>@endforeach
                            </select>
                            <input type="date" name="start_date" x-model="editItem.start_date" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <input type="date" name="end_date" x-model="editItem.end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="flex justify-end gap-3 mt-6"><button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button><button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button></div>
                    </form>
                </div>
            </div>
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <form :action="`/employee-shifts/${deleteId}`" method="POST" class="flex justify-center gap-3 mt-6">@csrf @method('DELETE')<button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button><button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button></form>
                </div>
            </div>
        </div>

        {{-- Leave Requests Section --}}
        <div x-data="{ createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null }" x-show="activeTab === 'leave_requests'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Leave Requests</h3>
                <button @click="createModal = true; editModal = false; editItem = { status: 'on_progress', inclusive_days: 1 }" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">+ Add</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Title</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Type</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Dates</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Days</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="text-right px-6 py-3 font-semibold text-gray-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($employee->leaveRequests as $leaveRequest)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-3 text-gray-900 dark:text-white font-medium">{{ $leaveRequest->title }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $leaveRequest->leaveType?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $leaveRequest->start_date->format('d M Y') }} - {{ $leaveRequest->end_date->format('d M Y') }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $leaveRequest->inclusive_days }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ str_replace('_', ' ', ucfirst($leaveRequest->status)) }}</td>
                            <td class="px-6 py-3 text-right">
                                <button @click="editModal = true; editItem = { id: {{ $leaveRequest->id }}, leave_type_id: @js($leaveRequest->leave_type_id), employee_delegation_id: @js($leaveRequest->employee_delegation_id), title: @js($leaveRequest->title), start_date: @js($leaveRequest->start_date->format('Y-m-d')), end_date: @js($leaveRequest->end_date->format('Y-m-d')), inclusive_days: @js($leaveRequest->inclusive_days), evidence: @js($leaveRequest->evidence ?? ''), reason: @js($leaveRequest->reason ?? ''), notes: @js($leaveRequest->notes ?? ''), status: @js($leaveRequest->status) }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                                <button @click="deleteModal = true; deleteId = {{ $leaveRequest->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No leave requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Leave Request' : 'Add Leave Request'"></h3>
                    <form :action="editModal ? `/leave-requests/${editItem.id}` : '/leave-requests'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="title" x-model="editItem.title" required placeholder="Title" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <select name="leave_type_id" x-model="editItem.leave_type_id" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="">Leave type...</option>@foreach($leaveTypes as $leaveType)<option value="{{ $leaveType->id }}">{{ $leaveType->name }}</option>@endforeach
                            </select>
                            <input type="date" name="start_date" x-model="editItem.start_date" required class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <input type="date" name="end_date" x-model="editItem.end_date" required class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <input type="number" name="inclusive_days" x-model="editItem.inclusive_days" min="0" required placeholder="Inclusive days" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <x-employee-async-select name="employee_delegation_id" model="editItem.employee_delegation_id" placeholder="Search delegated employee..." />
                            <select name="status" x-model="editItem.status" required class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="on_progress">On Progress</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <input type="text" name="evidence" x-model="editItem.evidence" placeholder="Evidence file path" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <textarea name="reason" x-model="editItem.reason" rows="3" placeholder="Reason" class="md:col-span-2 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"></textarea>
                            <textarea name="notes" x-model="editItem.notes" rows="3" placeholder="Notes" class="md:col-span-2 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="flex justify-end gap-3 mt-6"><button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button><button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700" x-text="editModal ? 'Update' : 'Save'"></button></div>
                    </form>
                </div>
            </div>
            <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                    <form :action="`/leave-requests/${deleteId}`" method="POST" class="flex justify-center gap-3 mt-6">@csrf @method('DELETE')<button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button><button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button></form>
                </div>
            </div>
        </div>

        @if ($employee->leaveBalance)
        <div x-show="activeTab === 'leave_balance'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Leave Balance</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-1">Total</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $employee->leaveBalance->total }} days</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-1">Used</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $employee->leaveBalance->used }} days</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-1">Remaining</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $employee->leaveBalance->remaining }} days</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-1">Extra</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $employee->leaveBalance->extra }} days</p>
                </div>
            </div>
        </div>
        @else
        <div x-show="activeTab === 'leave_balance'" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Leave Balance</h3>
            <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                No leave balance record found.
            </div>
        </div>
        @endif
    </div>
    @endsection