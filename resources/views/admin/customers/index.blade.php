@extends('admin.layouts.admin')

@section('title', 'Customers')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Customers</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage your SaaS customers (companies).</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.customers.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Search</label>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by name or email..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm"
                    >
                </div>
                <div>
                    <label for="plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plan</label>
                    <select id="plan" name="plan" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                        <option value="all" @selected(request('plan') === 'all' || ! request('plan'))>All Plans</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan }}" @selected(request('plan') === $plan)>{{ ucfirst($plan) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm">
                        <option value="all" @selected(request('status') === 'all' || ! request('status'))>All Status</option>
                        <option value="1" @selected(request('status') === '1')>Active</option>
                        <option value="0" @selected(request('status') === '0')>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition-all text-sm">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'plan', 'status']))
                    <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                            <th class="px-5 py-3">Company</th>
                            <th class="px-5 py-3">Plan</th>
                            <th class="px-5 py-3">Employees</th>
                            <th class="px-5 py-3">Subscription</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($customers as $company)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $company->email }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $company->plan === 'enterprise' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : ($company->plan === 'pro' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">
                                        {{ ucfirst($company->plan) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-700 dark:text-gray-300">{{ number_format($company->employees_count) }}</td>
                                <td class="px-5 py-4">
                                    @if ($company->subscription_expires_at)
                                        <span class="text-gray-700 dark:text-gray-300">{{ $company->subscription_expires_at->format('d M Y') }}</span>
                                        @if ($company->subscription_expires_at->isPast())
                                            <span class="block text-xs text-red-600 dark:text-red-400">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">Never</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $company->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                                        {{ $company->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.customers.show', $company) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $company) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No customers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($customers->hasPages())
            <div>
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
