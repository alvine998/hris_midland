@extends('layouts.app')

@section('title', 'Roles - ' . config('app.name'))

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Roles</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage user roles and permissions.</p>
    </div>
    <a href="{{ route('master-data.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add New
    </a>
</div>

@session('success')
<div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-300">{{ $value }}</div>
@endsession

<x-list-search :action="route('master-data.roles')" placeholder="Search role name or description" />

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Name</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Description</th>
                    <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Permissions</th>
                    <th class="text-right px-6 py-4 font-semibold text-gray-900 dark:text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($roles as $role)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">{{ $role->name }}</td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ Str::limit($role->description, 50) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5 max-w-md">
                            @forelse (($role->rbac ?? []) as $permission)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                    {{ $permission }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500 dark:text-gray-400">No permissions</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="inline-flex items-center gap-1">
                            <a href="{{ route('master-data.roles.edit', $role) }}" class="p-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>

                            <div x-data="{ show: false, id: null }">
                                <button @click="show = true; id = {{ $role->id }}" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.away="show = false">
                                    <div class="fixed inset-0 bg-gray-900/50" @click="show = false"></div>
                                    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this item? This action cannot be undone.</p>
                                        <form :action="`/master-data/roles/${id}`" method="POST" class="flex justify-center gap-3">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="show = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($roles->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $roles->links() }}</div>
    @endif
</div>
@endsection
