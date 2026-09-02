@extends('layouts.app')

@section('title', 'Survey Customer')

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null, isFromLead: false }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Survey Customer</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Track customer surveys and interest after lead follow-up.</p>
        </div>
        <button type="button" @click="open = true; edit = false; item = {status: 'scheduled', interest_level: 'medium'}; isFromLead = false" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Add Survey</button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>
    @endsession
    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $errors->first() }}</div>
    @endif

    <x-list-search :action="route('marketing-sales.survey-customers.index')" placeholder="Search by customer name, phone..." />

    <div class="flex flex-wrap gap-2">
        @foreach($statuses as $k=>$v)
            <a href="{{ route('marketing-sales.survey-customers.index', array_merge(request()->query(), ['status'=>$k])) }}" class="rounded-full px-3 py-1.5 text-xs font-medium border {{ request('status')===$k ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">{{ $v }}</a>
        @endforeach
        @if(request('status') || request('search'))
            <a href="{{ route('marketing-sales.survey-customers.index') }}" class="rounded-full px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">Clear</a>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Customer</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Project</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Unit</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Survey Date</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Rating</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Interest</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($surveys as $survey)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $survey->customer_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $survey->customer_phone ?? '-' }} · {{ $survey->lead?->name ? 'Lead: '.$survey->lead->name : 'Direct' }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $survey->project?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $survey->projectStock?->unit_code ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">{{ $survey->survey_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($survey->rating)
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-900/30 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-300">★ {{ $survey->rating }}/5</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4"><span class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize {{ $survey->interest_level==='high' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($survey->interest_level==='low' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300') }}">{{ $survey->interest_level ?? '-' }}</span></td>
                        <td class="px-6 py-4"><span class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $survey->status }}</span></td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" @click="open = true; edit = true; item = @js(['id'=>$survey->id,'lead_id'=>$survey->lead_id,'project_id'=>$survey->project_id,'project_stock_id'=>$survey->project_stock_id,'customer_name'=>$survey->customer_name,'customer_phone'=>$survey->customer_phone,'customer_email'=>$survey->customer_email,'survey_date'=>$survey->survey_date?->format('Y-m-d'),'surveyor_id'=>$survey->surveyor_id,'rating'=>$survey->rating,'interest_level'=>$survey->interest_level,'feedback'=>$survey->feedback,'next_action'=>$survey->next_action,'status'=>$survey->status]); isFromLead = !!item.lead_id" class="mr-2 font-medium text-indigo-600 dark:text-indigo-400">Edit</button>
                            <button type="button" @click="deleteId = {{ $survey->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No survey customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($surveys->hasPages())
        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $surveys->links() }}</div>
        @endif
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit Survey Customer' : 'Add Survey Customer'"></h3>
            <form method="POST" :action="edit ? `{{ url('/marketing-sales/survey-customers') }}/${item.id}` : '{{ route('marketing-sales.survey-customers.store') }}'">
                @csrf
                <input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="md:col-span-2 flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-4 py-3 cursor-pointer">
                        <input type="checkbox" x-model="isFromLead" @change="if (isFromLead) { item.customer_name=''; item.customer_phone=''; item.customer_email=''; } else { item.lead_id=''; }" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Is customer from Leads?</span>
                        <span class="ml-auto text-xs text-gray-500 dark:text-gray-400" x-text="isFromLead ? 'Select existing lead' : 'Input manual customer'"></span>
                        <input type="hidden" name="is_from_lead" :value="isFromLead ? 1 : 0">
                    </label>

                    <template x-if="isFromLead">
                        <div class="md:col-span-2">
                            <label class="block">
                                <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Select Customer (Leads) *</span>
                                <select name="lead_id" x-model="item.lead_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select lead</option>
                                    @foreach($leads as $lead)<option value="{{ $lead->id }}">{{ $lead->name }}@if($lead->phone) — {{ $lead->phone }}@endif @if($lead->email) ({{ $lead->email }})@endif</option>@endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Customer name, phone and email will be auto-filled from the selected lead.</p>
                            </label>
                        </div>
                    </template>

                    <template x-if="!isFromLead">
                        <div class="contents">
                            <label class="md:col-span-2">
                                <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Customer Name *</span>
                                <input type="text" name="customer_name" x-model="item.customer_name" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </label>
                            <label>
                                <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</span>
                                <input type="text" name="customer_phone" x-model="item.customer_phone" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </label>
                            <label>
                                <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</span>
                                <input type="email" name="customer_email" x-model="item.customer_email" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            </label>
                        </div>
                    </template>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Project</span>
                        <select name="project_id" x-model="item.project_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select project</option>
                            @foreach($projects as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Stock</span>
                        <select name="project_stock_id" x-model="item.project_stock_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select unit</option>
                            @foreach($projectStocks as $stock)<option value="{{ $stock->id }}">{{ $stock->unit_code }} (Project #{{ $stock->project_id }})</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Surveyor</span>
                        <select name="surveyor_id" x-model="item.surveyor_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select surveyor</option>
                            @foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Survey Date</span>
                        <input type="date" name="survey_date" x-model="item.survey_date" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Rating (1-5)</span>
                        <input type="number" name="rating" x-model="item.rating" min="1" max="5" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Interest Level</span>
                        <select name="interest_level" x-model="item.interest_level" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select</option>
                            @foreach($interestLevels as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</span>
                        <select name="status" x-model="item.status" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach($statuses as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Next Action</span>
                        <input type="text" name="next_action" x-model="item.next_action" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label class="md:col-span-2">
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Feedback</span>
                        <textarea name="feedback" x-model="item.feedback" rows="3" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Survey?</h3>
            <form :action="`{{ url('/marketing-sales/survey-customers') }}/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
