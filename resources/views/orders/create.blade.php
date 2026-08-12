@extends('layouts.guest')

@section('title', 'Order ' . $package->name . ' — ' . config('app.name'))

@section('content')
<div class="bg-[#FCFBF8] dark:bg-[#101413] min-h-screen">
    <div class="max-w-[720px] mx-auto px-6 py-10 sm:py-14">
        <a href="{{ route('landing') }}#pricing" class="text-sm text-stone-500 hover:text-stone-700 dark:text-stone-400">&larr; Back to pricing</a>
        <h1 class="mt-3 text-[28px] sm:text-[34px] font-bold tracking-tight">Order {{ $package->name }}</h1>
        <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">{{ $package->description }} — {{ ucfirst($package->plan) }} · {{ $package->max_employees ? $package->max_employees.' employees' : 'Unlimited' }}{{ $package->duration_days ? ' · '.$package->duration_days.' days' : '' }}</p>

        <div class="mt-6 rounded-2xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 p-5 flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-widest text-stone-500">Price</div>
                @if($package->isOnSale())
                    <div class="mt-1 flex items-baseline gap-2">
                        <span class="text-xl font-bold">Rp {{ number_format($package->effective_price, 0, ',', '.') }}</span>
                        <span class="text-sm line-through text-stone-500">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">-{{ $package->discount_percent }}%</span>
                    </div>
                @else
                    <div class="mt-1 text-xl font-bold">{{ $package->price == 0 ? 'Free' : 'Rp '.number_format($package->price, 0, ',', '.') }}</div>
                @endif
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full bg-stone-900 text-white dark:bg-white dark:text-stone-900">{{ ucfirst($package->plan) }}</span>
        </div>

        <form method="POST" action="{{ route('orders.store', $package) }}" class="mt-6 bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-6 space-y-4">
            @csrf
            <div>
                <label for="company_name" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Company name *</label>
                <input id="company_name" name="company_name" value="{{ old('company_name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('company_name') border-red-500 @enderror" placeholder="PT Example">
                @error('company_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Work email *</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('email') border-red-500 @enderror" placeholder="ops@company.com">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Phone / WhatsApp</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm" placeholder="08...">
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm" placeholder="Anything we should know...">{{ old('notes') }}</textarea>
            </div>
            <div>
                <label for="voucher_code" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Voucher code</label>
                <input id="voucher_code" name="voucher_code" value="{{ old('voucher_code') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm" placeholder="Enter code if you have one">
            </div>
            @if($package->effective_price > 0)
            <div class="pt-2 border-t border-stone-200 dark:border-stone-800 space-y-3">
                <p class="text-sm font-semibold text-stone-900 dark:text-stone-100">Payment method</p>
                {{-- ponytail: single method bank_transfer for now; add more methods by expanding choices + validation --}}
                <label class="flex items-center gap-2 text-sm"><input type="radio" name="payment_method" value="bank_transfer" checked class="text-[#12372A]"> Bank Transfer</label>
                @error('payment_method')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                <div>
                    <label for="bank_account_id" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Transfer to *</label>
                    <select id="bank_account_id" name="bank_account_id" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('bank_account_id') border-red-500 @enderror">
                        <option value="">Select bank account</option>
                        @foreach($bankAccounts as $ba)
                            <option value="{{ $ba->id }}" @selected(old('bank_account_id')==$ba->id)>{{ $ba->bank_name }} — {{ $ba->account_number }} ({{ $ba->account_holder }})</option>
                        @endforeach
                    </select>
                    @error('bank_account_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    @if($bankAccounts->isEmpty())<p class="mt-1 text-xs text-amber-600">No bank accounts configured. Contact admin.</p>@endif
                </div>
            </div>
            @else
                <input type="hidden" name="payment_method" value="bank_transfer">
            @endif
            <button type="submit" class="w-full h-11 rounded-full bg-[#12372A] text-white font-semibold hover:bg-[#1a4d3a] transition-colors">Place order</button>
            <p class="text-xs text-stone-500 text-center">No payment required now. Our team will contact you to activate.</p>
        </form>
    </div>
</div>
@endsection
