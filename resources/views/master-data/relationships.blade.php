@extends('layouts.app')

@section('title', 'Relationships - ' . config('app.name'))

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Relationships</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage relationship references for family and emergency contacts.</p>
        </div>
        <button @click="open = true; edit = false; item = {}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl">Add New</button>
    </div>
    @session('success')<div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-300">{{ $value }}</div>@endsession
    <x-list-search :action="route('master-data.relationships')" placeholder="Search relationship name or description" />
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700"><th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Name</th><th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Description</th><th class="text-right px-6 py-4 font-semibold text-gray-900 dark:text-white">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">@forelse($relationships as $relationship)<tr><td class="px-6 py-4 text-gray-900 dark:text-white font-medium">{{ $relationship->name }}</td><td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ Str::limit($relationship->description, 80) }}</td><td class="px-6 py-4 text-right"><button @click="open = true; edit = true; item = { id: {{ $relationship->id }}, name: @js($relationship->name), description: @js($relationship->description ?? '') }" class="text-indigo-600 dark:text-indigo-400 font-medium mr-3">Edit</button><button @click="deleteId = {{ $relationship->id }}" class="text-red-600 dark:text-red-400 font-medium">Delete</button></td></tr>@empty<tr><td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No relationships found.</td></tr>@endforelse</tbody>
        </table>
        @if($relationships->hasPages())<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">{{ $relationships->links() }}</div>@endif
    </div>
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"><div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div><div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6"><h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="edit ? 'Edit Relationship' : 'Add Relationship'"></h3><form method="POST" :action="edit ? `/master-data/relationships/${item.id}` : '{{ route('master-data.relationships.store') }}'">@csrf<input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'"><div class="space-y-4"><input name="name" x-model="item.name" required placeholder="Name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><textarea name="description" x-model="item.description" rows="3" placeholder="Description" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea></div><div class="flex justify-end gap-3 mt-6"><button type="button" @click="open = false" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg">Cancel</button><button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-lg" x-text="edit ? 'Update' : 'Save'"></button></div></form></div></div>
    <div x-show="deleteId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"><div class="fixed inset-0 bg-gray-900/50" @click="deleteId = null"></div><div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center"><h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3><form :action="`/master-data/relationships/${deleteId}`" method="POST" class="flex justify-center gap-3 mt-6">@csrf @method('DELETE')<button type="button" @click="deleteId = null" class="px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg">Cancel</button><button class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg">Delete</button></form></div></div>
</div>
@endsection
