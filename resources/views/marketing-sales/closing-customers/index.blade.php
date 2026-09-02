@extends('layouts.app')

@section('title', 'Closing Customer')

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null, isFromLead: false }" class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Closing Customer</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage customer closings and property deals.</p>
        </div>
        <button type="button" @click="open = true; edit = false; item = {status: 'pending', payment_method: 'cash'}; isFromLead = false" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Add Closing</button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>
    @endsession
    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $errors->first() }}</div>
    @endif

    <x-list-search :action="route('marketing-sales.closing-customers.index')" placeholder="Search by customer name, phone..." />

    <div class="flex flex-wrap gap-2">
        @foreach($statuses as $k=>$v)
            <a href="{{ route('marketing-sales.closing-customers.index', array_merge(request()->query(), ['status'=>$k])) }}" class="rounded-full px-3 py-1.5 text-xs font-medium border {{ request('status')===$k ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">{{ $v }}</a>
        @endforeach
        @if(request('status') || request('search'))
            <a href="{{ route('marketing-sales.closing-customers.index') }}" class="rounded-full px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">Clear</a>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Customer</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Project / Unit</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Amount</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Payment</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Sales</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Date</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($closings as $closing)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $closing->customer_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $closing->customer_phone ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 dark:text-gray-300">{{ $closing->project?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $closing->projectStock?->unit_code ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $closing->amount ? 'Rp '.number_format($closing->amount,0,',','.') : '-' }}</td>
                        <td class="px-6 py-4"><span class="rounded-full px-2 py-0.5 text-xs font-semibold uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $closing->payment_method ?? '-' }}</span></td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $closing->salesPerson?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">{{ $closing->closing_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-4"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $closing->status==='completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : ($closing->status==='cancelled' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300') }}">{{ $closing->status }}</span></td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" @click="open = true; edit = true; item = @js(['id'=>$closing->id,'lead_id'=>$closing->lead_id,'survey_customer_id'=>$closing->survey_customer_id,'customer_name'=>$closing->customer_name,'customer_phone'=>$closing->customer_phone,'customer_email'=>$closing->customer_email,'project_id'=>$closing->project_id,'project_stock_id'=>$closing->project_stock_id,'closing_date'=>$closing->closing_date?->format('Y-m-d'),'amount'=>$closing->amount,'payment_method'=>$closing->payment_method,'status'=>$closing->status,'sales_person_id'=>$closing->sales_person_id,'notes'=>$closing->notes]); isFromLead = !!item.lead_id" class="mr-2 font-medium text-indigo-600 dark:text-indigo-400">Edit</button>
                            <button type="button" @click="deleteId = {{ $closing->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No closing customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($closings->hasPages())
        <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $closings->links() }}</div>
        @endif
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit Closing' : 'Add Closing'"></h3>
            <form method="POST" :action="edit ? `{{ url('/marketing-sales/closing-customers') }}/${item.id}` : '{{ route('marketing-sales.closing-customers.store') }}'">
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
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Project *</span>
                        <select name="project_id" x-model="item.project_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select project</option>
                            @foreach($projects as $project)<option value="{{ $project->id }}">{{ $project->name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Stock</span>
                        <select name="project_stock_id" x-model="item.project_stock_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select unit</option>
                            @foreach($projectStocks as $stock)<option value="{{ $stock->id }}">{{ $stock->unit_code }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Survey Customer</span>
                        <select name="survey_customer_id" x-model="item.survey_customer_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select survey</option>
                            @foreach($surveyCustomers as $sc)<option value="{{ $sc->id }}">{{ $sc->customer_name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sales Person</span>
                        <select name="sales_person_id" x-model="item.sales_person_id" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select sales</option>
                            @foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Closing Date</span>
                        <input type="date" name="closing_date" x-model="item.closing_date" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</span>
                        <input type="number" step="0.01" name="amount" x-model="item.amount" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</span>
                        <select name="payment_method" x-model="item.payment_method" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select</option>
                            @foreach($paymentMethods as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</span>
                        <select name="status" x-model="item.status" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach($statuses as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Closing?</h3>
            <form :action="`{{ url('/marketing-sales/closing-customers') }}/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
