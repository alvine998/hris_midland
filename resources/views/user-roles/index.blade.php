@extends('layouts.app')

@section('title', 'User Roles - ' . config('app.name'))

@section('content')
<div x-data="{ createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Roles</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage user role assignments and permissions.</p>
        </div>
        <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
            Assign Role
        </button>
    </div>

    @session('success')
    <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
        {{ $value }}
    </div>
    @endsession

    <x-list-search :action="route('user-roles.index')" placeholder="Search user name, email, or role" />

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">User</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Email</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Role</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($userRoles as $userRole)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">{{ $userRole->user?->name }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $userRole->user?->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400">
                                {{ $userRole->role?->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="editModal = true; editItem = { id: {{ $userRole->id }}, user_id: {{ $userRole->user_id }}, role_id: {{ $userRole->role_id }} }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                            <button @click="deleteModal = true; deleteId = {{ $userRole->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No user roles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($userRoles->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $userRoles->links() }}
        </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="createModal = false; editModal = false">
        <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit User Role' : 'Assign Role'"></h3>
            <form :action="editModal ? `/user-roles/${editItem.id}` : '/user-roles'" method="POST">
                @csrf
                <input type="hidden" name="_method" :value="editModal ? 'PUT' : 'POST'">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                        <select name="user_id" x-model="editItem.user_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select user...</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select name="role_id" x-model="editItem.role_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select role...</option>
                            @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
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

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModal = false">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to remove this user role assignment? This action cannot be undone.</p>
            <form :action="`/user-roles/${deleteId}`" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
