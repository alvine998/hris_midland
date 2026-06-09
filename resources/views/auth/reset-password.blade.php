@extends('layouts.guest')

@section('title', 'Reset Password - ' . config('app.name'))

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                    {{ config('app.name') }}
                </a>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Reset your password
                </p>
            </div>

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf

                    {{-- Token --}}
                    <input type="hidden" name="token" value="{{ $token ?? '' }}">

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $email ?? '') }}"
                            required
                            autofocus
                            autocomplete="email"
                            readonly
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 transition-colors text-sm @error('email') border-red-500 dark:border-red-400 @enderror"
                        >
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            New Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 transition-colors text-sm @error('password') border-red-500 dark:border-red-400 @enderror"
                            placeholder="Enter new password"
                        >
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 transition-colors text-sm"
                            placeholder="Confirm new password"
                        >
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm shadow-indigo-200 dark:shadow-indigo-900/30 transition-all hover:shadow-md text-sm"
                    >
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
