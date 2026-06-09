@extends('layouts.app')

@section('title', 'Work Locations - ' . config('app.name'))

@section('content')
<div x-data="{ createModal: false, editModal: false, editItem: {}, deleteModal: false, deleteId: null }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Work Locations</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage office and site locations.</p>
        </div>
        <button @click="createModal = true; editModal = false; editItem = {}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
            Add New
        </button>
    </div>

    @session('success')
    <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
        {{ $value }}
    </div>
    @endsession

    <x-list-search :action="route('master-data.work-locations')" placeholder="Search location name, city, province, address, or type" />

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Name</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Address</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">City</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Province</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-900 dark:text-white">Type</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($workLocations as $location)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">{{ $location->name }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $location->address }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $location->city }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $location->province }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($location->type)
                                    @case('head_office') bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 @break
                                    @case('branch') bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 @break
                                    @case('site') bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 @break
                                    @default bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400
                                @endswitch
                            ">
                                {{ str_replace('_', ' ', ucfirst($location->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="editModal = true; editItem = { id: {{ $location->id }}, name: @js($location->name), address: @js($location->address ?? ''), city: @js($location->city), province: @js($location->province), type: @js($location->type), latitude: @js($location->latitude), longitude: @js($location->longitude), radius: @js($location->radius) }" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium text-sm mr-3">Edit</button>
                            <button @click="deleteModal = true; deleteId = {{ $location->id }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No work locations found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($workLocations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $workLocations->links() }}
        </div>
        @endif
    </div>

    <div x-show="createModal || editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="createModal = false; editModal = false">
        <div class="fixed inset-0 bg-gray-900/50" @click="createModal = false; editModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4" x-text="editModal ? 'Edit Work Location' : 'Add New Work Location'"></h3>
            <form :action="editModal ? `/master-data/work-locations/${editItem.id}` : '/master-data/work-locations'" method="POST">
                @csrf
                <input type="hidden" name="_method" x-bind:value="editModal ? 'PUT' : 'POST'">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" id="name" x-model="editItem.name" placeholder="Location name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                        <textarea name="address" id="address" x-model="editItem.address" rows="3" placeholder="Full address" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                        <input type="text" name="city" id="city" x-model="editItem.city" placeholder="City name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Province</label>
                        <input type="text" name="province" id="province" x-model="editItem.province" placeholder="Province name" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                        <input type="text" name="latitude" id="latitude" x-model="editItem.latitude" placeholder="-6.2088" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                        <input type="text" name="longitude" id="longitude" x-model="editItem.longitude" placeholder="106.8650" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="radius" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Radius</label>
                        <input type="number" name="radius" id="radius" x-model="editItem.radius" min="0" placeholder="Radius meters" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select name="type" id="type" x-model="editItem.type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="head_office">Head Office</option>
                            <option value="branch">Branch</option>
                            <option value="site">Site</option>
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

    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteModal = false">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteModal = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this work location? This action cannot be undone.</p>
            <form :action="`/master-data/work-locations/${deleteId}`" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
