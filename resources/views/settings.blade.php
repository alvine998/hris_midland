@extends('layouts.app')

@section('title', 'Settings - ' . config('app.name'))

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Settings</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage application settings and preferences.</p>
</div>

<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">General Settings</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Application settings are coming soon.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Permission RBAC</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage role permissions from the Roles master data page.</p>
            </div>
            <a href="{{ route('master-data.roles') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                Open Roles
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Preferences</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Notification settings are coming soon.</p>
    </div>
</div>
@endsection
