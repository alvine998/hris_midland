@extends('admin.layouts.admin')

@section('title', $company->name)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">&larr; Back to customers</a>
                <h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $company->name }}</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.customers.edit', $company) }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition-all text-sm">
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.customers.toggle-active', $company) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2.5 rounded-xl border font-semibold text-sm transition-colors {{ $company->is_active ? 'border-red-200 text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20' : 'border-green-200 text-green-600 hover:bg-green-50 dark:border-green-800 dark:text-green-400 dark:hover:bg-green-900/20' }}">
                        {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Plan</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100 capitalize">{{ $company->plan }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Employees</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($company->employees_count) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Max Employees</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $company->max_employees ? number_format($company->max_employees) : 'Unlimited' }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Subscription Expires</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $company->subscription_expires_at?->format('d M Y') ?? 'Never' }}</p>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Company Details</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-100">{{ $company->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-100">{{ $company->phone }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-0.5">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $company->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-gray-500 dark:text-gray-400">Address</dt>
                    <dd class="mt-0.5 font-medium text-gray-900 dark:text-gray-100">{{ $company->address ?: '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
