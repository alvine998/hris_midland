@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Projects</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage operation & construction projects.</p>
        </div>
        <button type="button" @click="open = true; edit = false; item = {status: 'planning', progress: 0}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Add Project</button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>
    @endsession
    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $errors->first() }}</div>
    @endif

    <x-list-search :action="route('operation-construction.projects.index')" placeholder="Search projects by name, code, location..." />

    <div class="flex flex-wrap gap-2">
        @foreach(['planning'=>'Planning','ongoing'=>'Ongoing','completed'=>'Completed','on_hold'=>'On Hold','cancelled'=>'Cancelled'] as $k=>$v)
            <a href="{{ route('operation-construction.projects.index', array_merge(request()->query(), ['status'=>$k])) }}" class="rounded-full px-3 py-1.5 text-xs font-medium border {{ request('status')===$k ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:bg-gray-50' }}">{{ $v }}</a>
        @endforeach
        @if(request('status'))
            <a href="{{ route('operation-construction.projects.index') }}" class="rounded-full px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">Clear</a>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Project</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Company</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Progress</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Dates</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $project->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $project->code ?? 'No code' }} · {{ $project->location ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $project->company?->name ?? '-' }}</td>
                        <td class="px-6 py-4"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_',' ',$project->status) }}</span></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-20 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden"><div class="h-full bg-indigo-500" style="width: {{ $project->progress }}%"></div></div>
                                <span class="text-xs font-medium">{{ $project->progress }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">{{ $project->start_date?->format('d M Y') ?? '-' }} — {{ $project->end_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('operation-construction.projects.show', $project) }}" class="mr-2 font-medium text-indigo-600 dark:text-indigo-400">Detail</a>
                            <button type="button" @click="open = true; edit = true; item = @js(['id'=>$project->id,'name'=>$project->name,'code'=>$project->code,'company_id'=>$project->company_id,'location'=>$project->location,'status'=>$project->status,'start_date'=>$project->start_date?->format('Y-m-d'),'end_date'=>$project->end_date?->format('Y-m-d'),'budget'=>$project->budget,'progress'=>$project->progress,'manager_id'=>$project->manager_id,'description'=>$project->description])" class="mr-2 font-medium text-amber-600 dark:text-amber-400">Edit</button>
                            <button type="button" @click="deleteId = {{ $project->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No projects found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($projects->hasPages())
        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $projects->links() }}</div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit Project' : 'Add Project'"></h3>
            <form method="POST" :action="edit ? `{{ url('/operation-construction/projects') }}/${item.id}` : '{{ route('operation-construction.projects.store') }}'">
                @csrf
                <input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="md:col-span-2">
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name *</span>
                        <input type="text" name="name" x-model="item.name" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Code</span>
                        <input type="text" name="code" x-model="item.code" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Location</span>
                        <input type="text" name="location" x-model="item.location" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Company</span>
                        <select name="company_id" x-model="item.company_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select company</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Manager</span>
                        <select name="manager_id" x-model="item.manager_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select manager</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</span>
                        <select name="status" x-model="item.status" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach($statuses as $k=>$v)
                            <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Progress % *</span>
                        <input type="number" name="progress" x-model="item.progress" min="0" max="100" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</span>
                        <input type="date" name="start_date" x-model="item.start_date" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</span>
                        <input type="date" name="end_date" x-model="item.end_date" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label class="md:col-span-2">
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Budget</span>
                        <input type="number" step="0.01" name="budget" x-model="item.budget" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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

    {{-- Delete Modal --}}
    <div x-show="deleteId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteId = null"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Are you sure you want to delete this project?</p>
            <form :action="`{{ url('/operation-construction/projects') }}/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
