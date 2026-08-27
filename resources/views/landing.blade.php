@extends('layouts.guest')

@section('title', config('app.name') . ' — Modern HRIS for Growing Teams')

@section('content')
    {{-- ============== Navigation ============== --}}
    <nav data-lenis-prevent
         class="w-full bg-white/70 dark:bg-gray-950/70 backdrop-blur-xl border-b border-gray-200/60 dark:border-gray-800/60 sticky top-0 z-50 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                    <span class="relative flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600 shadow-lg shadow-indigo-500/30 group-hover:shadow-indigo-500/50 transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                    </span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ config('app.name') }}
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-1">
                    <a href="#features" data-lenis-prevent class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100/60 dark:hover:bg-gray-800/60 transition-colors">Features</a>
                    <a href="#modules" data-lenis-prevent class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100/60 dark:hover:bg-gray-800/60 transition-colors">Modules</a>
                    <a href="#how-it-works" data-lenis-prevent class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100/60 dark:hover:bg-gray-800/60 transition-colors">How it works</a>
                    <a href="#testimonials" data-lenis-prevent class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100/60 dark:hover:bg-gray-800/60 transition-colors">Testimonials</a>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="darkMode = !darkMode" type="button"
                            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                            aria-label="Toggle dark mode">
                        <svg x-cloak x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-cloak x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white rounded-lg transition-colors">
                                Dashboard
                            </a>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 rounded-lg shadow-sm transition-colors">
                                Open app
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white rounded-lg transition-colors">
                                Sign In
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 rounded-lg shadow-sm transition-colors">
                                Get started
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- ============== Hero (split layout with product preview) ============== --}}
    <section class="relative overflow-hidden bg-white dark:bg-gray-950">
        {{-- Background: gradient + grid + orbs --}}
        <div class="parallax-bg absolute inset-0 bg-gradient-to-b from-indigo-50/60 via-white to-white dark:from-indigo-950/40 dark:via-gray-950 dark:to-gray-950 pointer-events-none"
             data-parallax-speed="0.05"></div>
        <div class="parallax-layer grid-pattern" data-parallax-speed="0.12"></div>
        <div class="parallax-layer" data-parallax-speed="0.25">
            <div class="orb orb-1 float-slower"></div>
        </div>
        <div class="parallax-layer" data-parallax-speed="0.35">
            <div class="orb orb-2 float-slow"></div>
        </div>
        <div class="parallax-layer" data-parallax-speed="0.20">
            <div class="orb orb-3 float-slow"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 sm:pt-20 sm:pb-28 lg:pt-24 lg:pb-32">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                {{-- Left: copy --}}
                <div class="lg:col-span-6 max-w-2xl">
                    <div data-reveal>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100/70 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 text-xs font-semibold tracking-wide ring-1 ring-indigo-200/60 dark:ring-indigo-500/20">
                            <span class="relative flex w-2 h-2">
                                <span class="absolute inline-flex w-full h-full rounded-full bg-indigo-500 opacity-75 animate-ping"></span>
                                <span class="relative inline-flex w-2 h-2 rounded-full bg-indigo-500"></span>
                            </span>
                            New • 360° Feedback is live
                        </span>
                    </div>

                    <h1 data-reveal data-reveal-delay="80"
                        class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white tracking-tight leading-[1.05]">
                        The HR platform
                        <span class="relative inline-block">
                            <span class="relative z-10 bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600 bg-clip-text text-transparent">your team</span>
                            <svg class="absolute -bottom-1 left-0 w-full h-3 text-indigo-300 dark:text-indigo-700/60" viewBox="0 0 200 12" fill="none" preserveAspectRatio="none">
                                <path d="M2 9 Q 50 1, 100 6 T 198 5" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none"/>
                            </svg>
                        </span>
                        <br>actually loves using.
                    </h1>

                    <p data-reveal data-reveal-delay="160"
                       class="mt-6 text-lg sm:text-xl text-gray-600 dark:text-gray-400 leading-relaxed max-w-xl">
                        One workspace for payroll, attendance, leave, performance, and employee records — built for HR teams that don't have time for clunky software.
                    </p>

                    <div data-reveal data-reveal-delay="240" class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('login') }}" class="group inline-flex items-center justify-center px-6 py-3.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold rounded-xl shadow-lg shadow-gray-900/10 dark:shadow-white/5 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            Start free trial
                            <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="#modules" data-lenis-prevent class="group inline-flex items-center justify-center px-6 py-3.5 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 font-semibold rounded-xl border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 transition-all">
                            <svg class="mr-2 w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            Watch 2-min tour
                        </a>
                    </div>

                    <div data-reveal data-reveal-delay="320" class="mt-10 flex items-center gap-4">
                        <div class="flex -space-x-2">
                            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-pink-400 to-rose-500 ring-2 ring-white dark:ring-gray-950 flex items-center justify-center text-xs font-semibold text-white">SK</span>
                            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 ring-2 ring-white dark:ring-gray-950 flex items-center justify-center text-xs font-semibold text-white">MR</span>
                            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 ring-2 ring-white dark:ring-gray-950 flex items-center justify-center text-xs font-semibold text-white">JL</span>
                            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 ring-2 ring-white dark:ring-gray-950 flex items-center justify-center text-xs font-semibold text-white">AR</span>
                        </div>
                        <div class="text-sm">
                            <div class="flex items-center gap-0.5 text-amber-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-900 dark:text-white">4.9/5</span> from 500+ HR teams
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Right: floating product preview --}}
                <div data-reveal data-reveal-delay="200" class="lg:col-span-6 relative">
                    <div class="relative">
                        {{-- Decorative gradient ring --}}
                        <div class="absolute -inset-6 bg-gradient-to-tr from-indigo-500/20 via-violet-500/20 to-fuchsia-500/20 blur-3xl rounded-[3rem]"></div>

                        {{-- Browser window mockup --}}
                        <div class="relative rounded-2xl bg-white dark:bg-gray-900 shadow-2xl shadow-gray-900/20 dark:shadow-black/50 ring-1 ring-gray-200/60 dark:ring-gray-800 overflow-hidden">
                            {{-- Browser chrome --}}
                            <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-900/60">
                                <div class="flex gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                                </div>
                                <div class="flex-1 mx-4">
                                    <div class="max-w-md mx-auto h-7 rounded-md bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 flex items-center px-3 text-xs text-gray-400">
                                        <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        app.hris-midland.com/dashboard
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                            </div>

                            {{-- App body --}}
                            <div class="grid grid-cols-12 min-h-[420px]">
                                {{-- Sidebar --}}
                                <aside class="col-span-3 bg-gray-50/80 dark:bg-gray-950/80 border-r border-gray-100 dark:border-gray-800 p-3 space-y-1">
                                    <div class="flex items-center gap-2 px-2 py-2">
                                        <div class="w-6 h-6 rounded-md bg-gradient-to-br from-indigo-500 to-violet-600"></div>
                                        <span class="text-xs font-bold text-gray-900 dark:text-white">Midland</span>
                                    </div>
                                    <div class="mt-4 space-y-1">
                                        <div class="flex items-center gap-2 px-2 py-1.5 rounded-md bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            <span class="text-xs font-medium">Dashboard</span>
                                        </div>
                                        @foreach (['Employees', 'Payroll', 'Attendance', 'Leave', 'Reports'] as $i => $item)
                                            <div class="flex items-center gap-2 px-2 py-1.5 rounded-md text-gray-500 dark:text-gray-400">
                                                <div class="w-3.5 h-3.5 rounded bg-gray-200 dark:bg-gray-800"></div>
                                                <span class="text-xs">{{ $item }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </aside>

                                {{-- Main panel --}}
                                <div class="col-span-9 p-4 bg-white dark:bg-gray-900">
                                    {{-- Header row --}}
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">Hi, Sarah 👋</p>
                                            <p class="text-[10px] text-gray-500">Here's what's happening today</p>
                                        </div>
                                        <span class="text-[10px] px-2 py-1 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-medium">Live</span>
                                    </div>

                                    {{-- Stat cards --}}
                                    <div class="grid grid-cols-3 gap-2 mb-4">
                                        <div class="p-2.5 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 ring-1 ring-indigo-100 dark:ring-indigo-500/20">
                                            <p class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium">Headcount</p>
                                            <p class="text-lg font-bold text-gray-900 dark:text-white">248</p>
                                            <p class="text-[10px] text-emerald-600 font-medium">+12 this month</p>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-violet-50 dark:bg-violet-500/10 ring-1 ring-violet-100 dark:ring-violet-500/20">
                                            <p class="text-[10px] text-violet-600 dark:text-violet-400 font-medium">Present</p>
                                            <p class="text-lg font-bold text-gray-900 dark:text-white">92%</p>
                                            <p class="text-[10px] text-emerald-600 font-medium">+3% vs last wk</p>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-fuchsia-50 dark:bg-fuchsia-500/10 ring-1 ring-fuchsia-100 dark:ring-fuchsia-500/20">
                                            <p class="text-[10px] text-fuchsia-600 dark:text-fuchsia-400 font-medium">On leave</p>
                                            <p class="text-lg font-bold text-gray-900 dark:text-white">8</p>
                                            <p class="text-[10px] text-gray-500 font-medium">2 pending</p>
                                        </div>
                                    </div>

                                    {{-- Mini chart --}}
                                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 ring-1 ring-gray-100 dark:ring-gray-800 mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-[10px] font-semibold text-gray-700 dark:text-gray-300">Weekly attendance</p>
                                            <span class="text-[10px] text-gray-500">This week</span>
                                        </div>
                                        <div class="flex items-end gap-1.5 h-16">
                                            @foreach ([40, 65, 50, 80, 70, 90, 75] as $h)
                                                <div class="flex-1 rounded-sm bg-gradient-to-t from-indigo-500 to-violet-500" style="height: {{ $h }}%"></div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Activity row --}}
                                    <div class="space-y-1.5">
                                        @foreach ([
                                            ['bg-emerald-500', 'Sarah Wijaya checked in', '08:02'],
                                            ['bg-indigo-500', 'Payroll run for March completed', '09:15'],
                                            ['bg-amber-500', 'Andi Pratama requested leave', '10:32'],
                                        ] as [$dot, $msg, $time])
                                            <div class="flex items-center gap-2 p-1.5 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                                <span class="text-[10px] text-gray-700 dark:text-gray-300 flex-1">{{ $msg }}</span>
                                                <span class="text-[10px] text-gray-400">{{ $time }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Floating badge: notification --}}
                        <div class="absolute -left-4 sm:-left-8 top-1/3 hidden sm:flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-900 rounded-xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-800 -rotate-3 float-slow">
                            <span class="flex w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold text-gray-900 dark:text-white">Payroll processed</p>
                                <p class="text-[10px] text-gray-500">248 employees • 2 min ago</p>
                            </div>
                        </div>

                        {{-- Floating badge: stat --}}
                        <div class="absolute -right-2 sm:-right-6 bottom-12 hidden sm:flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-900 rounded-xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-800 rotate-2 float-slower">
                            <span class="flex w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold text-gray-900 dark:text-white">Absenteeism</p>
                                <p class="text-[10px] text-emerald-600 font-medium">↓ 30% this quarter</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Logo strip --}}
            <div data-reveal data-reveal-delay="400" class="mt-20 sm:mt-24">
                <p class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                    Powering HR teams at
                </p>
                <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-x-8 gap-y-6 items-center">
                    @foreach (['TechCorp', 'BuildCo', 'StartupHub', 'Northwind', 'Acme', 'Globex'] as $logo)
                        <div class="flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            <span class="text-lg font-bold tracking-tight">{{ $logo }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============== Modules / Features grid ============== --}}
    <section id="modules" class="relative py-24 sm:py-32 bg-gray-50 dark:bg-gray-950 overflow-hidden">
        <div class="parallax-layer absolute -top-32 -right-32 w-96 h-96 rounded-full bg-indigo-200/40 dark:bg-indigo-900/20 blur-3xl pointer-events-none" data-parallax-speed="0.2"></div>
        <div class="parallax-layer absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-fuchsia-200/40 dark:bg-fuchsia-900/20 blur-3xl pointer-events-none" data-parallax-speed="0.15"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="max-w-2xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-4">Everything in one place</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white tracking-tight">
                    A complete HR suite, not a collection of tools.
                </h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Eight tightly-integrated modules that work together from day one. No spreadsheets. No copy-paste between apps.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Card 1: Employee Management (highlighted) --}}
                <div data-reveal data-reveal-delay="0"
                     class="group relative p-6 bg-gradient-to-br from-indigo-600 to-violet-700 rounded-2xl shadow-xl shadow-indigo-500/20 hover:shadow-2xl hover:shadow-indigo-500/30 hover:-translate-y-1 transition-all overflow-hidden lg:col-span-2">
                    <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute right-4 top-4 px-2.5 py-1 rounded-md bg-white/15 backdrop-blur text-[10px] font-semibold text-white ring-1 ring-white/20">Most popular</div>
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/15 backdrop-blur rounded-xl flex items-center justify-center mb-5 ring-1 ring-white/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Employee management</h3>
                        <p class="text-indigo-100 leading-relaxed max-w-md">
                            One source of truth for every employee — contracts, documents, family, education, work history, and emergency contacts. All searchable, all in one place.
                        </p>
                        <div class="mt-6 flex items-center gap-4 text-white/90 text-sm">
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Documents</span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Contracts</span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Org chart</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Payroll --}}
                <div data-reveal data-reveal-delay="80"
                     class="group relative p-6 bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-gray-800 hover:ring-indigo-300 dark:hover:ring-indigo-700 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Payroll processing</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Run payroll in minutes. Auto-calculate deductions, generate payslips, and export to your bank — all from a single screen.
                    </p>
                </div>

                {{-- Card 3: Attendance --}}
                <div data-reveal data-reveal-delay="160"
                     class="group relative p-6 bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-gray-800 hover:ring-indigo-300 dark:hover:ring-indigo-700 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-500/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Attendance & shifts</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        One-tap check-in, multi-shift support, and live dashboards. See who's in, who's late, and who's on leave — in real time.
                    </p>
                </div>

                {{-- Card 4: Leave (highlighted) --}}
                <div data-reveal data-reveal-delay="240"
                     class="group relative p-6 bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-gray-800 hover:ring-indigo-300 dark:hover:ring-indigo-700 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 bg-sky-100 dark:bg-sky-500/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H5v12z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Leave management</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Custom leave types, multi-step approvals, and a shared holiday calendar. Employees request in 10 seconds, you approve in two clicks.
                    </p>
                </div>

                {{-- Card 5: Performance --}}
                <div data-reveal data-reveal-delay="320"
                     class="group relative p-6 bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-gray-800 hover:ring-indigo-300 dark:hover:ring-indigo-700 hover:shadow-xl hover:-translate-y-1 transition-all lg:col-span-2">
                    <div class="grid sm:grid-cols-2 gap-6 items-center">
                        <div>
                            <div class="w-12 h-12 bg-violet-100 dark:bg-violet-500/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Performance & 360° feedback</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Set KPIs, run 360° review cycles, and track growth over time. Built-in templates mean your first review cycle takes an afternoon, not a quarter.
                            </p>
                        </div>
                        {{-- Mini visualization --}}
                        <div class="p-4 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800/50 ring-1 ring-gray-200 dark:ring-gray-700">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Team performance</span>
                                <span class="text-emerald-600 font-semibold">+8.4%</span>
                            </div>
                            <div class="space-y-2">
                                @foreach ([['Andi', 92, 'bg-emerald-500'], ['Sarah', 88, 'bg-indigo-500'], ['Made', 76, 'bg-amber-500'], ['Lia', 84, 'bg-violet-500']] as [$name, $val, $color])
                                    <div>
                                        <div class="flex justify-between text-[10px] mb-1">
                                            <span class="text-gray-600 dark:text-gray-400">{{ $name }}</span>
                                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $val }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                            <div class="h-full rounded-full {{ $color }}" style="width: {{ $val }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Secondary features row --}}
            <div data-reveal data-reveal-delay="400" class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach ([
                    ['icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Reports & exports', 'desc' => 'Custom reports, CSV/PDF export'],
                    ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'title' => 'Role-based access', 'desc' => 'Granular permissions per module'],
                    ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'WhatsApp notifications', 'desc' => 'Reach employees where they are'],
                ] as $f)
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 hover:ring-indigo-300 dark:hover:ring-indigo-700 transition-all">
                        <span class="flex w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $f['title'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $f['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============== Features (benefits, "Why Midland") ============== --}}
    <section id="features" class="relative py-24 sm:py-32 bg-white dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="max-w-2xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-4">Why teams choose us</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Built for the way HR actually works.
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 items-center">
                {{-- Left: feature list --}}
                <div class="space-y-8">
                    @php
                        $whyFeatures = [
                            ['key' => 'indigo',  'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Lightning fast', 'desc' => 'Designed to be quicker than the spreadsheet you\'re trying to escape. Average task completion in under 30 seconds.'],
                            ['key' => 'emerald', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Secure by default', 'desc' => 'Encrypted at rest and in transit. Granular role-based access. Full audit log of every action.'],
                            ['key' => 'violet',  'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', 'title' => 'Works the way you do', 'desc' => 'Custom leave types, approval chains, and workflows. We adapt to your organization, not the other way around.'],
                        ];
                        $colorMap = [
                            'indigo'  => ['bg' => 'bg-indigo-100',  'bgDark' => 'dark:bg-indigo-500/10',  'text' => 'text-indigo-600',  'textDark' => 'dark:text-indigo-400'],
                            'emerald' => ['bg' => 'bg-emerald-100', 'bgDark' => 'dark:bg-emerald-500/10', 'text' => 'text-emerald-600', 'textDark' => 'dark:text-emerald-400'],
                            'violet'  => ['bg' => 'bg-violet-100',  'bgDark' => 'dark:bg-violet-500/10',  'text' => 'text-violet-600',  'textDark' => 'dark:text-violet-400'],
                        ];
                    @endphp
                    @foreach ($whyFeatures as $i => $f)
                        @php $c = $colorMap[$f['key']]; @endphp
                        <div data-reveal data-reveal-delay="{{ $i * 100 }}" class="flex gap-4">
                            <div class="shrink-0 w-11 h-11 rounded-xl {{ $c['bg'] }} {{ $c['bgDark'] }} flex items-center justify-center">
                                <svg class="w-5 h-5 {{ $c['text'] }} {{ $c['textDark'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $f['title'] }}</h3>
                                <p class="mt-1.5 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $f['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right: stat callout card --}}
                <div data-reveal data-reveal-delay="200" class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-indigo-500/10 via-violet-500/10 to-fuchsia-500/10 blur-2xl rounded-3xl"></div>
                    <div class="relative p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 shadow-2xl">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="stat-number-light text-5xl font-bold">10k+</p>
                                <p class="mt-2 text-sm text-gray-300">Employees managed daily</p>
                            </div>
                            <div>
                                <p class="stat-number-light text-5xl font-bold">500+</p>
                                <p class="mt-2 text-sm text-gray-300">Companies trust us</p>
                            </div>
                            <div>
                                <p class="stat-number-light text-5xl font-bold">99.9%</p>
                                <p class="mt-2 text-sm text-gray-300">Uptime SLA</p>
                            </div>
                            <div>
                                <p class="stat-number-light text-5xl font-bold">4.9<span class="text-2xl">/5</span></p>
                                <p class="mt-2 text-sm text-gray-300">Average user rating</p>
                            </div>
                        </div>
                        <div class="mt-8 pt-6 border-t border-white/10">
                            <div class="flex items-center gap-3">
                                <span class="flex w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </span>
                                <p class="text-sm text-gray-300">
                                    <span class="font-semibold text-white">"The best HRIS we've used."</span><br>
                                    <span class="text-gray-400">— Most mentioned in 2025 reviews</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============== How it works ============== --}}
    <section id="how-it-works" class="relative py-24 sm:py-32 bg-gray-50 dark:bg-gray-950 overflow-hidden">
        <div class="parallax-bg absolute inset-0 opacity-30 pointer-events-none"
             style="background-image: radial-gradient(circle at 20% 30%, rgba(99,102,241,0.12), transparent 50%), radial-gradient(circle at 80% 70%, rgba(139,92,246,0.10), transparent 50%);"
             data-parallax-speed="0.08"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="max-w-2xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-4">Up and running in days</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Three steps. Then it's just HR.
                </h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Most teams go from signup to first payroll in under a week.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 relative">
                {{-- Connecting line --}}
                <div class="hidden md:block absolute top-12 left-1/6 right-1/6 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-700 to-transparent"></div>

                @foreach ([
                    ['n' => '1', 'title' => 'Set up your organization', 'desc' => 'Add your company, departments, and roles. Import from CSV or connect to your existing tools.', 'time' => '~ 15 min'],
                    ['n' => '2', 'title' => 'Bring your team in', 'desc' => 'Bulk-import employees or invite them by email. They fill in their own records from a simple form.', 'time' => '~ 1 hour'],
                    ['n' => '3', 'title' => 'Run your first payroll', 'desc' => 'Configure your payroll rules once, then run payroll in two clicks. Forever.', 'time' => 'Day 1'],
                ] as $i => $step)
                    <div data-reveal data-reveal-delay="{{ $i * 150 }}" class="relative p-8 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all">
                        <div class="flex items-center justify-between mb-6">
                            <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white font-bold text-lg shadow-lg shadow-indigo-500/30">
                                {{ $step['n'] }}
                            </span>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $step['time'] }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============== Testimonials ============== --}}
    <section id="testimonials" class="relative py-24 sm:py-32 bg-white dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="max-w-2xl mx-auto text-center mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-4">Loved by HR teams</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Don't take our word for it.
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                @foreach ([
                    ['quote' => 'HRIS Midland transformed how we handle our HR operations. Payroll automation alone saved us 20 hours a month.', 'name' => 'Sarah Kim', 'role' => 'HR Director, TechCorp', 'initials' => 'SK', 'grad' => 'from-pink-400 to-rose-500'],
                    ['quote' => 'The attendance tracking is a game changer. We reduced absenteeism by 30% in the first quarter.', 'name' => 'Michael Reyes', 'role' => 'Operations Manager, BuildCo', 'initials' => 'MR', 'grad' => 'from-amber-400 to-orange-500'],
                    ['quote' => 'The reporting suite gives us exactly the insights we need for strategic workforce planning. Highly recommended.', 'name' => 'Jennifer Lim', 'role' => 'CEO, StartupHub', 'initials' => 'JL', 'grad' => 'from-emerald-400 to-teal-500'],
                ] as $i => $t)
                    <div data-reveal data-reveal-delay="{{ $i * 120 }}"
                         class="relative p-6 bg-gray-50 dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all">
                        {{-- Quote mark --}}
                        <svg class="absolute top-6 right-6 w-10 h-10 text-indigo-100 dark:text-indigo-900/40" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11h4v10H0z"/>
                        </svg>
                        <div class="flex items-center gap-1 mb-4">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6">"{{ $t['quote'] }}"</p>
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                            <span class="w-10 h-10 rounded-full bg-gradient-to-br {{ $t['grad'] }} flex items-center justify-center text-sm font-semibold text-white">{{ $t['initials'] }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $t['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $t['role'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============== CTA ============== --}}
    <section class="relative py-24 sm:py-32 overflow-hidden">
        <div class="parallax-bg absolute inset-0 bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-700" data-parallax-speed="0.05"></div>
        <div class="parallax-layer absolute top-10 left-10 w-96 h-96 rounded-full bg-white/10 blur-3xl" data-parallax-speed="0.25"></div>
        <div class="parallax-layer absolute bottom-10 right-10 w-96 h-96 rounded-full bg-fuchsia-300/20 blur-3xl" data-parallax-speed="0.2"></div>
        <div class="parallax-layer absolute inset-0 grid-pattern-dark pointer-events-none" data-parallax-speed="0.1"></div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 data-reveal class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white tracking-tight">
                Ready to give your HR team a break?
            </h2>
            <p data-reveal data-reveal-delay="120" class="mt-5 text-lg text-indigo-100 leading-relaxed max-w-2xl mx-auto">
                Start your 14-day free trial. No credit card required. Migrate from your current tool in under a week with our onboarding team.
            </p>
            <div data-reveal data-reveal-delay="240" class="mt-10 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-7 py-3.5 bg-white text-indigo-700 font-semibold rounded-xl shadow-lg shadow-indigo-900/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    Start free trial
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="#modules" data-lenis-prevent class="inline-flex items-center justify-center px-7 py-3.5 bg-white/10 backdrop-blur text-white font-semibold rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                    Talk to sales
                </a>
            </div>
            <p data-reveal data-reveal-delay="320" class="mt-6 text-sm text-indigo-200">
                Free for teams up to 10 · No credit card · Cancel anytime
            </p>
        </div>
    </section>

    {{-- ============== Footer ============== --}}
    <footer class="bg-gray-950 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                <div class="col-span-2 md:col-span-2">
                    <div class="flex items-center gap-2.5 mb-4">
                        <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-600">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                        </span>
                        <span class="text-lg font-bold text-white">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-sm leading-relaxed max-w-xs">
                        Modern HR management platform for growing teams. Built with care in Indonesia.
                    </p>
                    <div class="mt-6 flex gap-3">
                        @foreach (['M23 4a9.97 9.97 0 00-3.84-1.13.07.07 0 00-.07.04c-.21.37-.45.87-.61 1.26a9.2 9.2 0 00-3.27 0 6.1 6.1 0 00-.62-1.26.07.07 0 00-.07-.04A9.97 9.97 0 003.16 4a.07.07 0 00-.03.03C.53 9.05-.32 13.97.1 18.84a.08.08 0 00.03.05 10 10 0 003 1.52.07.07 0 00.08-.03c.77-1.05 1.45-2.16 2.03-3.34a.07.07 0 00-.04-.1 6.6 6.6 0 01-1.87-.85.07.07 0 010-.12c.13-.1.25-.2.37-.3a.07.07 0 01.07-.01 7.18 7.18 0 006.13 0 .07.07 0 01.08.01c.12.1.24.2.37.3a.07.07 0 010 .12 6.3 6.3 0 01-1.87.85.07.07 0 00-.04.1c.59 1.18 1.27 2.29 2.03 3.34a.07.07 0 00.08.03 9.97 9.97 0 003-1.52.08.08 0 00.03-.05c.5-5.59-.84-10.45-3.55-14.43a.06.06 0 00-.03-.03zM8 15.34c-.62 0-1.13-.56-1.13-1.25s.5-1.25 1.13-1.25 1.13.56 1.13 1.25-.5 1.25-1.13 1.25zm5.34 0c-.62 0-1.13-.56-1.13-1.25s.5-1.25 1.13-1.25 1.13.56 1.13 1.25-.5 1.25-1.13 1.25z', 'M22.46 6c-.85.38-1.78.64-2.73.76 1-.6 1.76-1.55 2.12-2.68-.93.55-1.96.95-3.06 1.17A4.82 4.82 0 0016.05 4c-2.65 0-4.8 2.15-4.8 4.8 0 .38.04.75.13 1.1A13.65 13.65 0 011.64 4.5a4.8 4.8 0 001.49 6.41 4.77 4.77 0 01-2.17-.6v.06c0 2.35 1.67 4.31 3.88 4.76a4.82 4.82 0 01-2.17.08 4.81 4.81 0 004.49 3.34A9.65 9.65 0 010 20.13 13.6 13.6 0 007.36 22c8.85 0 13.69-7.33 13.69-13.69 0-.21 0-.42-.01-.62A9.78 9.78 0 0024 5.05a9.6 9.6 0 01-1.54 0z', 'M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.95v5.66H9.36V9h3.41v1.56h.05c.47-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 11-.01-4.12 2.06 2.06 0 01.01 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.72V1.72C24 .77 23.2 0 22.22 0z'] as $d)
                            <a href="#" class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $d }}"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Product</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#modules" data-lenis-prevent class="hover:text-white transition-colors">Modules</a></li>
                        <li><a href="#features" data-lenis-prevent class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Changelog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Company</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Customers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Resources</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Help center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <div class="flex items-center gap-2 text-sm">
                    <span class="relative flex w-2 h-2">
                        <span class="absolute inline-flex w-full h-full rounded-full bg-emerald-500 opacity-75 animate-ping"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    All systems operational
                </div>
            </div>
        </div>
    </footer>
@endsection

@push('scripts')
    @vite(['resources/js/landing.js'])
@endpush
