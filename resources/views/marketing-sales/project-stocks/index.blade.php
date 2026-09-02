@extends('layouts.app')

@section('title', 'Project Stock')

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Project Stock <span class="text-sm font-normal text-gray-500 dark:text-gray-400">(Property)</span></h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage property units and inventory per project.</p>
        </div>
        <button type="button" @click="open = true; edit = false; item = {status: 'available'}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Add Stock</button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>
    @endsession
    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $errors->first() }}</div>
    @endif

    <x-list-search :action="route('marketing-sales.project-stocks.index')" placeholder="Search by unit code, type, block..." />

    <div class="flex flex-wrap gap-2">
        @foreach($statuses as $k=>$v)
            <a href="{{ route('marketing-sales.project-stocks.index', array_merge(request()->query(), ['status'=>$k])) }}" class="rounded-full px-3 py-1.5 text-xs font-medium border {{ request('status')===$k ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">{{ $v }}</a>
        @endforeach
        @if(request('status') || request('search') || request('project_id'))
            <a href="{{ route('marketing-sales.project-stocks.index') }}" class="rounded-full px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">Clear</a>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Project</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Unit Code</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Type / Block</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Size</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Price</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($stocks as $stock)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $stock->project?->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono text-gray-700 dark:text-gray-300">{{ $stock->unit_code }}</td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 dark:text-gray-300">{{ $stock->type ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Block {{ $stock->block ?? '-' }} · {{ $stock->bedrooms ?? 0 }}BR / {{ $stock->bathrooms ?? 0 }}BA</p>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">Land {{ $stock->land_size ?? '-' }} m²<br>Building {{ $stock->building_size ?? '-' }} m²</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">{{ $stock->price ? 'Rp '.number_format($stock->price,0,',','.') : '-' }}</td>
                        <td class="px-6 py-4"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $stock->status==='available' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($stock->status==='sold' ? 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300' : ($stock->status==='booked' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300')) }}">{{ $stock->status }}</span></td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" @click="open = true; edit = true; item = @js(['id'=>$stock->id,'project_id'=>$stock->project_id,'unit_code'=>$stock->unit_code,'type'=>$stock->type,'block'=>$stock->block,'land_size'=>$stock->land_size,'building_size'=>$stock->building_size,'bedrooms'=>$stock->bedrooms,'bathrooms'=>$stock->bathrooms,'price'=>$stock->price,'status'=>$stock->status,'description'=>$stock->description])" class="mr-2 font-medium text-indigo-600 dark:text-indigo-400">Edit</button>
                            <button type="button" @click="deleteId = {{ $stock->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No project stocks yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stocks->hasPages())
        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $stocks->links() }}</div>
        @endif
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit Project Stock' : 'Add Project Stock'"></h3>
            <form method="POST" :action="edit ? `{{ url('/marketing-sales/project-stocks') }}/${item.id}` : '{{ route('marketing-sales.project-stocks.store') }}'">
                @csrf
                <input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Project *</span>
                        <select name="project_id" x-model="item.project_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select project</option>
                            @foreach($projects as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Code *</span>
                        <input type="text" name="unit_code" x-model="item.unit_code" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. A-01-05">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</span>
                        <input type="text" name="type" x-model="item.type" placeholder="e.g. Residential, Commercial" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Block</span>
                        <input type="text" name="block" x-model="item.block" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Land Size (m²)</span>
                        <input type="number" step="0.01" name="land_size" x-model="item.land_size" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Building Size (m²)</span>
                        <input type="number" step="0.01" name="building_size" x-model="item.building_size" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Bedrooms</span>
                        <input type="number" name="bedrooms" x-model="item.bedrooms" min="0" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Bathrooms</span>
                        <input type="number" name="bathrooms" x-model="item.bathrooms" min="0" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Price</span>
                        <input type="number" step="0.01" name="price" x-model="item.price" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</span>
                        <select name="status" x-model="item.status" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach($statuses as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </label>
                    <label class="md:col-span-2">
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</span>
                        <textarea name="description" x-model="item.description" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" x-text="edit ? 'Update' : 'Create'"></button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteId = null"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Stock?</h3>
            <form :action="`{{ url('/marketing-sales/project-stocks') }}/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
