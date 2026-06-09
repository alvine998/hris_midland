@extends('layouts.guest')

@section('title', 'Forgot Password - ' . config('app.name'))

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                    {{ config('app.name') }}
                </a>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Forgot your password?
                </p>
            </div>

            {{-- Status Message --}}
            @session('status')
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-300">
                    {{ $value }}
                </div>
            @endsession

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                    Enter your email address and we will send you a password reset link.
                </p>

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 transition-colors text-sm @error('email') border-red-500 dark:border-red-400 @enderror"
                            placeholder="you@company.com"
                        >
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm shadow-indigo-200 dark:shadow-indigo-900/30 transition-all hover:shadow-md text-sm"
                    >
                        Send Reset Link
                    </button>
                </form>

                {{-- Back to login --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
                        Back to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
