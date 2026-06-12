@extends('layouts.app')

@section('title', 'Create Role - ' . config('app.name'))

@section('content')
<div class="mb-6">
    <a href="{{ route('master-data.roles') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">&larr; Back to Roles</a>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Create Role</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Set the role name and RBAC permissions.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 sm:p-8">
    <form method="POST" action="{{ route('master-data.roles.store') }}">
        @csrf

        @include('master-data.roles._form', [
            'role' => null,
            'permissionGroups' => $permissionGroups,
            'submitLabel' => 'Create Role',
        ])
    </form>
</div>
@endsection
