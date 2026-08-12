@extends('layouts.company')

@section('title', 'Dashboard — ' . config('app.name'))

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold">Welcome, {{ Auth::guard('company')->user()->name }}</h1>
            <p class="text-sm text-stone-600 dark:text-stone-400 mt-1">Your workspace is active.</p>
        </div>
        <form method="POST" action="{{ route('company.logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-full border border-stone-300 dark:border-stone-600 text-sm font-medium hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">Sign out</button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php $company = Auth::guard('company')->user(); @endphp
        <div class="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-5">
            <div class="text-xs uppercase tracking-widest text-stone-500 mb-2">Plan</div>
            <div class="text-lg font-bold">{{ ucfirst($company->plan) }}</div>
        </div>
        <div class="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-5">
            <div class="text-xs uppercase tracking-widest text-stone-500 mb-2">Employees</div>
            <div class="text-lg font-bold">{{ $company->employees()->count() }} / {{ $company->max_employees ?? '∞' }}</div>
        </div>
        <div class="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-5">
            <div class="text-xs uppercase tracking-widest text-stone-500 mb-2">Status</div>
            <div class="text-lg font-bold capitalize">{{ $company->status }}</div>
        </div>
    </div>

    <div class="mt-8 bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-6">
        <h2 class="text-lg font-semibold mb-4">Your Information</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-stone-500">Email</span><span class="font-medium">{{ $company->email }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Phone</span><span class="font-medium">{{ $company->phone }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Address</span><span class="font-medium">{{ $company->address }}</span></div>
            @if($company->subscription_expires_at)
            <div class="flex justify-between"><span class="text-stone-500">Subscription expires</span><span class="font-medium">{{ $company->subscription_expires_at->format('d M Y') }}</span></div>
            @endif
        </div>
    </div>
</div>
@endsection
