@extends('admin.layouts.admin')

@section('title', 'Edit - ' . $company->name)

@section('content')
    <div class="max-w-2xl space-y-6">
        {{-- Header --}}
        <div>
            <a href="{{ route('admin.customers.show', $company) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">&larr; Back to customer</a>
            <h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">Edit {{ $company->name }}</h2>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.customers.update', $company) }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Company Name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $company->name) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm @error('name') border-red-500 dark:border-red-400 @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $company->email) }}"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm @error('email') border-red-500 dark:border-red-400 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone', $company->phone) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm"
                    >
                </div>

                <div>
                    <label for="plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plan</label>
                    <select
                        id="plan"
                        name="plan"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm"
                    >
                        @foreach ($plans as $plan)
                            <option value="{{ $plan }}" @selected(old('plan', $company->plan) === $plan)>{{ ucfirst($plan) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="max_employees" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Max Employees</label>
                    <input
                        id="max_employees"
                        type="number"
                        name="max_employees"
                        value="{{ old('max_employees', $company->max_employees) }}"
                        min="1"
                        placeholder="Leave empty for unlimited"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm"
                    >
                </div>

                <div>
                    <label for="subscription_expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Subscription Expires At</label>
                    <input
                        id="subscription_expires_at"
                        type="date"
                        name="subscription_expires_at"
                        value="{{ old('subscription_expires_at', $company->subscription_expires_at?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm"
                    >
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                    <input
                        id="address"
                        type="text"
                        name="address"
                        value="{{ old('address', $company->address) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors text-sm"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 cursor-pointer"
                            @checked(old('is_active', $company->is_active))
                        >
                        <span class="text-sm text-gray-600 dark:text-gray-400">Customer is active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition-all text-sm">
                    Save Changes
                </button>
                <a href="{{ route('admin.customers.show', $company) }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
