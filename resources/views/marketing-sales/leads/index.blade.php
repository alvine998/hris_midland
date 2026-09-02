@extends('layouts.app')

@section('title', 'Leads')

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Leads</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage marketing & sales leads — track prospect pipeline.</p>
        </div>
        <button type="button" @click="open = true; edit = false; item = {status: 'new', source: 'website'}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Add Lead</button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>
    @endsession
    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $errors->first() }}</div>
    @endif

    <x-list-search :action="route('marketing-sales.leads.index')" placeholder="Search leads by name, phone, email..." />

    <div class="flex flex-wrap gap-2">
        @foreach($statuses as $k=>$v)
            <a href="{{ route('marketing-sales.leads.index', array_merge(request()->query(), ['status'=>$k])) }}" class="rounded-full px-3 py-1.5 text-xs font-medium border {{ request('status')===$k ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">{{ $v }}</a>
        @endforeach
        @if(request('status') || request('source') || request('search'))
            <a href="{{ route('marketing-sales.leads.index') }}" class="rounded-full px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">Clear filters</a>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Customer</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Contact</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Project Interest</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Assigned</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Follow Up</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $lead->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->source ? ucfirst(str_replace('_',' ',$lead->source)) : 'No source' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 dark:text-gray-300">{{ $lead->phone ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->email ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $lead->project?->name ?? '-' }}</td>
                        <td class="px-6 py-4"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $lead->status==='converted' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($lead->status==='lost' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300') }}">{{ str_replace('_',' ',$lead->status) }}</span></td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $lead->assignedEmployee?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">{{ $lead->follow_up_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" @click="open = true; edit = true; item = @js(['id'=>$lead->id,'name'=>$lead->name,'phone'=>$lead->phone,'email'=>$lead->email,'source'=>$lead->source,'project_id'=>$lead->project_id,'assigned_to'=>$lead->assigned_to,'status'=>$lead->status,'budget_min'=>$lead->budget_min,'budget_max'=>$lead->budget_max,'notes'=>$lead->notes,'follow_up_date'=>$lead->follow_up_date?->format('Y-m-d')])" class="mr-2 font-medium text-indigo-600 dark:text-indigo-400">Edit</button>
                            <button type="button" @click="deleteId = {{ $lead->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No leads found. Create your first lead.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $leads->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit Lead' : 'Add Lead'"></h3>
            <form method="POST" :action="edit ? `{{ url('/marketing-sales/leads') }}/${item.id}` : '{{ route('marketing-sales.leads.store') }}'">
                @csrf
                <input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="md:col-span-2">
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</span>
                        <input type="text" name="name" x-model="item.name" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</span>
                        <input type="text" name="phone" x-model="item.phone" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
                        <input type="email" name="email" x-model="item.email" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Source</span>
                        <select name="source" x-model="item.source" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select source</option>
                            @foreach($sources as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Project Interest</span>
                        <select name="project_id" x-model="item.project_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select project</option>
                            @foreach($projects as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned To</span>
                        <select name="assigned_to" x-model="item.assigned_to" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Unassigned</option>
                            @foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</span>
                        <select name="status" x-model="item.status" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach($statuses as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Budget Min</span>
                        <input type="number" step="0.01" name="budget_min" x-model="item.budget_min" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Budget Max</span>
                        <input type="number" step="0.01" name="budget_max" x-model="item.budget_max" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Follow Up Date</span>
                        <input type="date" name="follow_up_date" x-model="item.follow_up_date" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label class="md:col-span-2">
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</span>
                        <textarea name="notes" x-model="item.notes" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Lead?</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">This action cannot be undone.</p>
            <form :action="`{{ url('/marketing-sales/leads') }}/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
