@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Companies</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalCompanies) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Active Companies</p>
                <p class="mt-1 text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($activeCompanies) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Employees</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalEmployees) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalUsers) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Recent companies --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent Companies</h2>
                    <a href="{{ route('admin.customers.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors">View all</a>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($recentCompanies as $company)
                        <div class="flex items-center justify-between py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $company->name }}</p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $company->email }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $company->plan === 'enterprise' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : ($company->plan === 'pro' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">
                                    {{ ucfirst($company->plan) }}
                                </span>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $company->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">No companies yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Plan distribution --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Plan Distribution</h2>

                <div class="space-y-4">
                    @foreach (['enterprise', 'pro', 'free'] as $plan)
                        @php($total = $planDistribution->where('plan', $plan)->first()?->total ?? 0)
                        @php($percentage = $totalCompanies > 0 ? round(($total / $totalCompanies) * 100) : 0)
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $plan }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $total }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                <div class="h-full rounded-full {{ $plan === 'enterprise' ? 'bg-purple-500' : ($plan === 'pro' ? 'bg-blue-500' : 'bg-gray-400') }}" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
