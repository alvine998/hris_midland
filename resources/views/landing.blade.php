@extends('layouts.guest')

@section('title', config('app.name') . ' — HRIS for Teams that Ship')

@section('content')
<div class="bg-[#FCFBF8] dark:bg-[#101413] text-stone-900 dark:text-stone-100 selection:bg-[#12372A] selection:text-white">

    {{-- NAV --}}
    <nav class="sticky top-0 z-50 backdrop-blur-xl bg-[#FCFBF8]/80 dark:bg-[#101413]/80 border-b border-stone-200/70 dark:border-stone-800">
        <div class="max-w-[1200px] mx-auto px-6 h-[64px] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#12372A] flex items-center justify-center text-white font-bold tracking-tight text-[14px]">T</div>
                <span class="font-semibold tracking-tight text-[16px]">{{ config('app.name') }}</span>
                <span class="hidden sm:inline-flex ml-3 text-[11px] tracking-widest uppercase font-medium px-2 py-0.5 rounded-full bg-stone-900 text-stone-50 dark:bg-stone-100 dark:text-stone-900">v2 · Inland</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="#features" class="hidden md:inline-flex text-sm font-medium px-3 py-2 text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100">Product</a>
                <a href="#pricing" class="hidden md:inline-flex text-sm font-medium px-3 py-2 text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100">Pricing</a>
                <a href="#faq" class="hidden md:inline-flex text-sm font-medium px-3 py-2 text-stone-600 dark:text-stone-400 hover:text-stone-900 dark:hover:text-stone-100">FAQ</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center h-9 px-4 rounded-full bg-[#12372A] text-white text-sm font-medium hover:bg-[#1a4d3a] transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center h-9 px-4 rounded-full bg-[#12372A] text-white text-sm font-medium hover:bg-[#1a4d3a] transition-colors">Sign in</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[900px] h-[600px] bg-[radial-gradient(60%_60%_at_50%_0%,#d5e8dc_0%,transparent_70%)] dark:bg-[radial-gradient(60%_60%_at_50%_0%,#1d3127_0%,transparent_70%)] opacity-70"></div>
            <div class="absolute top-16 right-[-120px] w-[460px] h-[460px] rounded-full bg-[#e8f0e9] dark:bg-[#1a2a22] blur-[80px] opacity-60"></div>
        </div>

        <div class="relative max-w-[1200px] mx-auto px-6 pt-14 pb-10 sm:pt-24 sm:pb-20">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr] gap-10 lg:gap-12 items-center">
                {{-- Left copy --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 text-[12px] font-medium shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        Built for ops, not decks — attendance, shifts, leave, payroll, KPI
                    </div>
                    <h1 class="mt-6 text-[34px] sm:text-[56px] font-[700] tracking-[-0.04em] leading-[0.95] text-stone-900 dark:text-stone-50">
                        HRIS that looks like
                        <span class="text-[#12372A] dark:text-[#8fbf9f]">your actual work.</span>
                    </h1>
                    <p class="mt-5 text-[17px] leading-7 text-stone-600 dark:text-stone-400 max-w-xl">
                        Targetin runs companies, departments, shifts, attendance check-in/out, leave approvals, payroll periods & 360 feedback — all in one Laravel-native workspace. No spreadsheet glue.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-full bg-[#12372A] text-white text-[15px] font-semibold shadow-[0_12px_24px_-12px_rgba(18,55,42,0.6)] hover:bg-[#1a4d3a] hover:-translate-y-px transition-all">
                            Open workspace
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="#features" class="inline-flex items-center justify-center h-11 px-6 rounded-full bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 text-[15px] font-medium hover:border-stone-300 dark:hover:border-stone-700 transition-colors">
                            See real modules
                        </a>
                    </div>
                    <div class="mt-6 flex items-center gap-4 text-[12.5px] text-stone-500 dark:text-stone-400">
                        <span class="inline-flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-[#12372A] text-white flex items-center justify-center text-[10px]">✓</span> OTP + audit logs</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-[#12372A] text-white flex items-center justify-center text-[10px]">✓</span> Import / export attendance</span>
                    </div>
                </div>

                {{-- Right artifact --}}
                <div class="relative lg:pl-4">
                    <div class="absolute -inset-6 bg-gradient-to-b from-white to-stone-100 dark:from-stone-900 dark:to-[#151a17] rounded-[28px] -z-10 blur-[1px] border border-stone-200/60 dark:border-stone-800"></div>

                    {{-- Browser artifact --}}
                    <div class="rounded-[18px] overflow-hidden border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 shadow-[0_20px_80px_-20px_rgba(0,0,0,0.35),0_0_0_1px_rgba(0,0,0,0.04)]">
                        {{-- chrome --}}
                        <div class="h-9 flex items-center gap-2 px-4 border-b border-stone-200 dark:border-stone-800 bg-stone-50/80 dark:bg-stone-900">
                            <span class="w-3 h-3 rounded-full bg-[#ff5f57]"></span>
                            <span class="w-3 h-3 rounded-full bg-[#ffbd2e]"></span>
                            <span class="w-3 h-3 rounded-full bg-[#28ca42]"></span>
                            <div class="ml-4 hidden sm:flex items-center gap-2 text-[11px] text-stone-500">
                                <span class="px-2.5 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">targetin.hris / attendances</span>
                                <span class="w-1 h-1 rounded-full bg-stone-300"></span>
                                <span>Live</span>
                            </div>
                        </div>
                        {{-- body --}}
                        <div class="grid grid-cols-[58px_1fr] sm:grid-cols-[72px_1fr] min-h-[380px]">
                            {{-- mini sidebar --}}
                            <div class="border-r border-stone-200 dark:border-stone-800 bg-[#FCFBF8] dark:bg-[#111413] p-2 flex flex-col gap-1">
                                <div class="w-full h-8 rounded-lg bg-[#12372A]"></div>
                                <div class="mt-3 space-y-1">
                                    <div class="h-7 rounded-md bg-stone-900 dark:bg-stone-100"></div>
                                    <div class="h-7 rounded-md bg-stone-100 dark:bg-stone-800 flex items-center px-2 gap-2"><span class="w-2 h-2 rounded-full bg-emerald-600"></span></div>
                                    <div class="h-7 rounded-md"></div>
                                    <div class="h-7 rounded-md"></div>
                                    <div class="h-7 rounded-md"></div>
                                </div>
                                <div class="mt-auto h-8 rounded-full bg-stone-200 dark:bg-stone-800"></div>
                            </div>
                            {{-- main --}}
                            <div class="p-4 sm:p-5 bg-white dark:bg-stone-900">
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="text-[11px] px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 border border-emerald-200/60 dark:border-emerald-900">Present today 84%</span>
                                    <span class="text-[11px] px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-200 border border-amber-200/60 dark:border-amber-900">3 pending leaves</span>
                                    <span class="text-[11px] px-2.5 py-1 rounded-full bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-300 border border-stone-200 dark:border-stone-700">Shift: Morning · 08:00-16:00</span>
                                </div>
                                <div class="grid grid-cols-3 gap-3 mb-5">
                                    <div class="rounded-xl border border-stone-200 dark:border-stone-800 p-3 bg-[#FCFBF8] dark:bg-stone-900/50">
                                        <div class="text-[11px] uppercase tracking-widest text-stone-500">Employees</div>
                                        <div class="mt-1 text-xl font-bold tracking-tight">2,412</div>
                                        <div class="mt-1 h-1 rounded-full bg-stone-200 dark:bg-stone-800"><div class="h-1 w-[78%] bg-[#12372A] rounded-full"></div></div>
                                    </div>
                                    <div class="rounded-xl border border-stone-200 dark:border-stone-800 p-3">
                                        <div class="text-[11px] uppercase tracking-widest text-stone-500">Check-ins</div>
                                        <div class="mt-1 text-xl font-bold tracking-tight">1,803</div>
                                        <div class="mt-1 text-[11px] text-emerald-700 dark:text-emerald-300">+12% vs yesterday</div>
                                    </div>
                                    <div class="rounded-xl border border-stone-200 dark:border-stone-800 p-3">
                                        <div class="text-[11px] uppercase tracking-widest text-stone-500">Payroll close</div>
                                        <div class="mt-1 text-xl font-bold tracking-tight">3 days</div>
                                        <div class="mt-1 text-[11px] text-stone-500">Period: Apr 1-15</div>
                                    </div>
                                </div>
                                <div class="rounded-xl border border-stone-200 dark:border-stone-800 overflow-hidden">
                                    <div class="px-3 py-2.5 flex items-center justify-between bg-stone-50 dark:bg-stone-800/50 text-[12px] font-medium">
                                        <span>Today · Attendance</span>
                                        <span class="text-[11px] text-stone-500">Import / Export enabled</span>
                                    </div>
                                    <div class="divide-y divide-stone-100 dark:divide-stone-800 text-[12.5px]">
                                        <div class="flex items-center justify-between px-3 py-2.5">
                                            <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-stone-200 dark:bg-stone-700 flex items-center justify-center text-[10px] font-bold">AS</span><div><div class="font-medium leading-none">Ayu S.</div><div class="text-[11px] text-stone-500">Dept: Engineering · Shift M</div></div></div>
                                            <span class="px-2 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-[11px] border border-emerald-200 dark:border-emerald-800">08:02 IN</span>
                                        </div>
                                        <div class="flex items-center justify-between px-3 py-2.5">
                                            <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-stone-200 dark:bg-stone-700 flex items-center justify-center text-[10px] font-bold">RK</span><div><div class="font-medium leading-none">Riko K.</div><div class="text-[11px] text-stone-500">Dept: Ops · Shift A</div></div></div>
                                            <span class="px-2 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-[11px] border border-amber-200 dark:border-amber-800">Leave · Pending</span>
                                        </div>
                                        <div class="flex items-center justify-between px-3 py-2.5">
                                            <div class="flex items-center gap-2.5"><span class="w-6 h-6 rounded-full bg-stone-200 dark:bg-stone-700 flex items-center justify-center text-[10px] font-bold">ML</span><div><div class="font-medium leading-none">Mira L.</div><div class="text-[11px] text-stone-500">Dept: HR · Shift M</div></div></div>
                                            <span class="px-2 py-1 rounded-full bg-stone-100 dark:bg-stone-800 text-stone-600 dark:text-stone-300 text-[11px] border border-stone-200 dark:border-stone-700">17:05 OUT</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-2 text-[11px] text-stone-500 dark:text-stone-400">
                        <span class="inline-flex h-5 items-center gap-1 px-2 rounded-full bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800">⌘K quick switch</span>
                        <span class="inline-flex h-5 items-center gap-1 px-2 rounded-full bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800">Audit logs →</span>
                    </div>
                </div>
            </div>

            {{-- strip stats --}}
            <div class="mt-12 grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-[900px]">
                <div class="rounded-2xl bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 p-4">
                    <div class="text-[11px] uppercase tracking-widest text-stone-500">Modules</div>
                    <div class="mt-1 text-[22px] font-bold tracking-tight">12 live</div>
                    <div class="mt-1 text-[12px] text-stone-500">Payroll, shifts, KPI, 360, chats…</div>
                </div>
                <div class="rounded-2xl bg-[#12372A] text-stone-50 p-4">
                    <div class="text-[11px] uppercase tracking-widest text-stone-300">Uptime</div>
                    <div class="mt-1 text-[22px] font-bold tracking-tight">99.9%</div>
                    <div class="mt-1 text-[12px] text-stone-300">Same stack you audit daily</div>
                </div>
                <div class="rounded-2xl bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 p-4">
                    <div class="text-[11px] uppercase tracking-widest text-stone-500">Import</div>
                    <div class="mt-1 text-[22px] font-bold tracking-tight">CSV/XLSX</div>
                    <div class="mt-1 text-[12px] text-stone-500">Attendances bulk + export</div>
                </div>
                <div class="rounded-2xl bg-white dark:bg-stone-900 border border-stone-200 dark:border-stone-800 p-4">
                    <div class="text-[11px] uppercase tracking-widest text-stone-500">Security</div>
                    <div class="mt-1 text-[22px] font-bold tracking-tight">OTP · Roles</div>
                    <div class="mt-1 text-[12px] text-stone-500">Policies, throttles, logs</div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="relative border-t border-stone-200/70 dark:border-stone-800 bg-white dark:bg-[#0E1210]">
        <div class="max-w-[1200px] mx-auto px-6 py-16 sm:py-24">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 text-[11px] tracking-widest uppercase font-semibold px-2.5 py-1 rounded-full bg-stone-900 text-white dark:bg-white dark:text-stone-900">Real modules · No mockups</div>
                <h2 class="mt-5 text-[30px] sm:text-[42px] font-bold tracking-[-0.03em] leading-[1.05]">Everything your spreadsheet pretended to do.</h2>
                <p class="mt-4 text-[16px] leading-7 text-stone-600 dark:text-stone-400">These are actual routes in this codebase. Not marketing fluff.</p>
            </div>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="group rounded-[20px] border border-stone-200 dark:border-stone-800 p-6 bg-[#FCFBF8] dark:bg-stone-900 hover:border-stone-300 dark:hover:border-stone-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-[#12372A] text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="mt-4 text-[16px] font-semibold">Employees & Contracts</h3>
                    <p class="mt-2 text-[14px] leading-6 text-stone-600 dark:text-stone-400">Full profile, families, education, work history, documents, emergency contacts + contract types & history with audit.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">employees.show</span>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">contracts.*</span>
                    </div>
                </div>

                <div class="group rounded-[20px] border border-stone-200 dark:border-stone-800 p-6 bg-white dark:bg-stone-900 hover:border-stone-300 dark:hover:border-stone-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M9 7h1m-1 4h1m4-4h1m-1 4h1"/></svg>
                    </div>
                    <h3 class="mt-4 text-[16px] font-semibold">Organization Graph</h3>
                    <p class="mt-2 text-[14px] leading-6 text-stone-600 dark:text-stone-400">Companies → work locations → departments → divisions → sections. Real FK hierarchy used by leave approvals.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[11px] px-2 py-1 rounded-full bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700">master-data.organizations</span>
                    </div>
                </div>

                <div class="group rounded-[20px] border border-stone-200 dark:border-stone-800 p-6 bg-[#FCFBF8] dark:bg-stone-900 hover:border-stone-300 dark:hover:border-stone-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mt-4 text-[16px] font-semibold">Attendance & Shifts</h3>
                    <p class="mt-2 text-[14px] leading-6 text-stone-600 dark:text-stone-400">Check-in/out UI, employee shifts, bulk import/export CSV/XLSX, template download. Built for real ops.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">attendances.check-in</span>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">attendances.import</span>
                    </div>
                </div>

                <div class="group rounded-[20px] border border-stone-200 dark:border-stone-800 p-6 bg-white dark:bg-stone-900 hover:border-stone-300 dark:hover:border-stone-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H5v12z"/></svg>
                    </div>
                    <h3 class="mt-4 text-[16px] font-semibold">Leave & Holidays</h3>
                    <p class="mt-2 text-[14px] leading-6 text-stone-600 dark:text-stone-400">Leave requests + bulk create, leave settings, approvals chaining, leave types, holiday calendar.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[11px] px-2 py-1 rounded-full bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700">leave-requests.bulk-store</span>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-stone-50 dark:bg-stone-800 border border-stone-200 dark:border-stone-700">holidays.*</span>
                    </div>
                </div>

                <div class="group rounded-[20px] border border-stone-200 dark:border-stone-800 p-6 bg-[#12372A] text-white hover:bg-[#1a4d3a] transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8v8"/></svg>
                    </div>
                    <h3 class="mt-4 text-[16px] font-semibold text-white">Payroll Periods</h3>
                    <p class="mt-2 text-[14px] leading-6 text-stone-200">Payroll periods, payrolls, payslip lifecycle. Designed to plug into approvals + attendance aggregates.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white/10 border border-white/10">payrolls</span>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white/10 border border-white/10">payroll-periods</span>
                    </div>
                </div>

                <div class="group rounded-[20px] border border-stone-200 dark:border-stone-800 p-6 bg-[#FCFBF8] dark:bg-stone-900 hover:border-stone-300 dark:hover:border-stone-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/></svg>
                    </div>
                    <h3 class="mt-4 text-[16px] font-semibold">Performance & Ops</h3>
                    <p class="mt-2 text-[14px] leading-6 text-stone-600 dark:text-stone-400">KPI, 360 feedback, employee tasks (complete/reopen), transfers, facilities, performance report.</p>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">kpis</span>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">feedback360</span>
                        <span class="text-[11px] px-2 py-1 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">employee-tasks</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="relative bg-[#FCFBF8] dark:bg-[#101413] border-y border-stone-200/70 dark:border-stone-800">
        <div class="max-w-[1200px] mx-auto px-6 py-16 sm:py-20 grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-[28px] sm:text-[36px] font-bold tracking-[-0.03em] leading-[1.1]">From first import to daily ops in 3 steps.</h2>
                <div class="mt-8 space-y-6">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-[#12372A] text-white flex items-center justify-center text-sm font-bold shrink-0">1</div>
                        <div><div class="font-semibold">Seed organization</div><div class="mt-1 text-[14px] leading-6 text-stone-600 dark:text-stone-400">Create companies, work locations, departments, divisions, sections, job positions, levels. One-time structure.</div></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 flex items-center justify-center text-sm font-bold shrink-0">2</div>
                        <div><div class="font-semibold">Import employees & shifts</div><div class="mt-1 text-[14px] leading-6 text-stone-600 dark:text-stone-400">Bulk add via reference master data, assign shifts & approval workflows, enable OTP login.</div></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 flex items-center justify-center text-sm font-bold shrink-0">3</div>
                        <div><div class="font-semibold">Run attendance + leave</div><div class="mt-1 text-[14px] leading-6 text-stone-600 dark:text-stone-400">Daily check-in/out, leave requests with approvals, holidays, payroll close, reports & activity logs.</div></div>
                    </div>
                </div>
            </div>
            <div class="rounded-[20px] border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 p-4 sm:p-6 shadow-sm">
                <div class="text-[12px] tracking-widest uppercase font-semibold text-stone-500">Sample data map</div>
                <div class="mt-4 grid grid-cols-2 gap-3 text-[13px]">
                    <div class="rounded-xl bg-[#FCFBF8] dark:bg-stone-900 border border-stone-200 dark:border-stone-800 p-4">
                        <div class="font-medium">Company A</div>
                        <div class="mt-2 space-y-1 text-stone-600 dark:text-stone-400">
                            <div>↳ Plant Cikarang</div>
                            <div class="pl-3">↳ Engineering</div>
                            <div class="pl-6">↳ Backend</div>
                            <div class="pl-9">↳ Squad Payments</div>
                        </div>
                    </div>
                    <div class="rounded-xl bg-stone-900 dark:bg-stone-100 text-white dark:text-stone-900 p-4">
                        <div class="font-medium text-white dark:text-stone-900">Shift</div>
                        <div class="mt-2 text-[12px] leading-5 text-stone-300 dark:text-stone-600">
                            Morning 08:00-16:00<br/>Afternoon 14:00-22:00<br/>Approval: Manager → HR
                        </div>
                        <div class="mt-3 inline-flex text-[11px] px-2 py-1 rounded-full bg-white/10 dark:bg-stone-900/10 border border-white/10 dark:border-stone-300">approval-workflows</div>
                    </div>
                </div>
                <div class="mt-3 rounded-xl border border-dashed border-stone-300 dark:border-stone-700 p-3 text-[12px] text-stone-600 dark:text-stone-400">CSV template includes NIK, name, dept, division, section, shift — matches <code class="px-1 py-0.5 rounded bg-stone-100 dark:bg-stone-800">attendances.template</code> route.</div>
            </div>
        </div>
    </section>

    {{-- PRICING --}}
    <section id="pricing" class="bg-white dark:bg-[#0E1210] border-y border-stone-200/70 dark:border-stone-800">
        <div class="max-w-[1200px] mx-auto px-6 py-16 sm:py-24">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 text-[11px] tracking-widest uppercase font-semibold px-2.5 py-1 rounded-full bg-[#12372A] text-white">Pricing · Order directly</div>
                <h2 class="mt-4 text-[30px] sm:text-[42px] font-bold tracking-[-0.03em] leading-[1.05]">Pick a package. Order in one click.</h2>
                <p class="mt-4 text-[15px] leading-6 text-stone-600 dark:text-stone-400">Choose the plan that fits your team size. Sale prices shown automatically when active.</p>
            </div>

            @if($packages->isEmpty())
                <div class="mt-10 rounded-2xl border border-dashed border-stone-300 dark:border-stone-700 p-10 text-center text-sm text-stone-500">No packages yet. Create one in <span class="font-mono">admin/packages</span>.</div>
            @else
                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($packages as $pkg)
                        <div class="rounded-[20px] border {{ $pkg->plan==='pro' ? 'border-[#12372A] shadow-[0_12px_40px_-16px_rgba(18,55,42,0.35)]' : 'border-stone-200 dark:border-stone-800' }} bg-[#FCFBF8] dark:bg-stone-900 p-6 flex flex-col">
                            <div class="flex items-center justify-between">
                                <span class="text-xs px-2.5 py-1 rounded-full {{ $pkg->plan==='enterprise' ? 'bg-purple-100 text-purple-700' : ($pkg->plan==='pro' ? 'bg-[#12372A] text-white' : 'bg-stone-200 dark:bg-stone-700 text-stone-700 dark:text-stone-200') }}">{{ ucfirst($pkg->plan) }}</span>
                                @if($pkg->isOnSale())<span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">Sale -{{ $pkg->discount_percent }}%</span>@endif
                            </div>
                            <h3 class="mt-3 text-lg font-bold">{{ $pkg->name }}</h3>
                            @if($pkg->description)<p class="mt-1 text-sm text-stone-600 dark:text-stone-400">{{ $pkg->description }}</p>@endif
                            <div class="mt-4">
                                @if($pkg->isOnSale())
                                    <div class="flex items-baseline gap-2"><span class="text-2xl font-bold">Rp {{ number_format($pkg->effective_price,0,',','.') }}</span><span class="text-sm line-through text-stone-500">Rp {{ number_format($pkg->price,0,',','.') }}</span></div>
                                    @if($pkg->sale_ends_at)<p class="text-xs text-amber-700 dark:text-amber-300">Sale ends {{ $pkg->sale_ends_at->format('d M Y H:i') }}</p>@endif
                                @else
                                    <div class="text-2xl font-bold">{{ $pkg->price==0 ? 'Free' : 'Rp '.number_format($pkg->price,0,',','.') }}</div>
                                @endif
                                <div class="mt-1 text-xs text-stone-500">{{ $pkg->max_employees ? $pkg->max_employees.' employees' : 'Unlimited employees' }}{{ $pkg->duration_days ? ' · '.$pkg->duration_days.' days' : '' }}</div>
                            </div>
                            <a href="{{ route('orders.create', $pkg->slug) }}" class="mt-6 inline-flex items-center justify-center h-11 rounded-full {{ $pkg->plan==='pro' ? 'bg-[#12372A] text-white hover:bg-[#1a4d3a]' : 'bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 hover:border-stone-300' }} text-sm font-semibold transition-colors">Order {{ $pkg->name }}</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="bg-white dark:bg-[#0E1210] border-b border-stone-200/70 dark:border-stone-800">
        <div class="max-w-[1200px] mx-auto px-6 py-16 sm:py-24">
            <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-10">
                <div>
                    <h2 class="text-[28px] sm:text-[40px] font-bold tracking-[-0.03em] leading-[1.05]">FAQ for implementers.</h2>
                    <p class="mt-4 text-[15px] leading-6 text-stone-600 dark:text-stone-400">No sales theater. Direct answers from codebase.</p>
                    <div class="mt-6 inline-flex items-center gap-2 px-3 py-2 rounded-full bg-[#FCFBF8] dark:bg-stone-900 border border-stone-200 dark:border-stone-800 text-[12px]">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Laravel 12 · PHP 8.4 · Vite · Pest
                    </div>
                </div>
                <div x-data="{ open: 1 }" class="divide-y divide-stone-200 dark:divide-stone-800 rounded-[20px] border border-stone-200 dark:border-stone-800 overflow-hidden bg-[#FCFBF8] dark:bg-stone-900">
                    <div class="p-0">
                        <button @click="open = open === 1 ? null : 1" class="w-full flex items-start justify-between gap-4 p-5 text-left">
                            <span class="font-medium text-[15px]">How is attendance modeled?</span>
                            <span class="w-7 h-7 rounded-full border border-stone-200 dark:border-stone-700 flex items-center justify-center shrink-0" x-text="open===1 ? '−' : '+'"></span>
                        </button>
                        <div x-show="open===1" x-collapse class="px-5 pb-5 text-[14px] leading-6 text-stone-600 dark:text-stone-400">
                            Attendance records per employee per day with check-in/out timestamps, source tracking, plus <code class="px-1 rounded bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">attendances.index / export / import / template</code> routes. EmployeeShift links users to Shifts.
                        </div>
                    </div>
                    <div class="p-0">
                        <button @click="open = open === 2 ? null : 2" class="w-full flex items-start justify-between gap-4 p-5 text-left">
                            <span class="font-medium text-[15px]">Leave approval chain?</span>
                            <span class="w-7 h-7 rounded-full border border-stone-200 dark:border-stone-700 flex items-center justify-center shrink-0" x-text="open===2 ? '−' : '+'"></span>
                        </button>
                        <div x-show="open===2" x-collapse class="px-5 pb-5 text-[14px] leading-6 text-stone-600 dark:text-stone-400">
                            LeaveRequest → LeaveApproval with approval_workflows master data. Supports bulkCreate for same range, leave settings for balance deduction, holidays exclusion.
                        </div>
                    </div>
                    <div class="p-0">
                        <button @click="open = open === 3 ? null : 3" class="w-full flex items-start justify-between gap-4 p-5 text-left">
                            <span class="font-medium text-[15px]">What about payroll?</span>
                            <span class="w-7 h-7 rounded-full border border-stone-200 dark:border-stone-700 flex items-center justify-center shrink-0" x-text="open===3 ? '−' : '+'"></span>
                        </button>
                        <div x-show="open===3" x-collapse class="px-5 pb-5 text-[14px] leading-6 text-stone-600 dark:text-stone-400">
                            Generic admin-crud resources <code class="px-1 rounded bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">payroll-periods</code> and <code class="px-1 rounded bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700">payrolls</code> — list, store, update, destroy. Ready to extend with calculations, deductions, and payslips.
                        </div>
                    </div>
                    <div class="p-0">
                        <button @click="open = open === 4 ? null : 4" class="w-full flex items-start justify-between gap-4 p-5 text-left">
                            <span class="font-medium text-[15px]">Security & roles?</span>
                            <span class="w-7 h-7 rounded-full border border-stone-200 dark:border-stone-700 flex items-center justify-center shrink-0" x-text="open===4 ? '−' : '+'"></span>
                        </button>
                        <div x-show="open===4" x-collapse class="px-5 pb-5 text-[14px] leading-6 text-stone-600 dark:text-stone-400">
                            Throttled login, OTP verification, password reset, RBAC via modules/roles/user_roles + permission middleware. Activity logs + login_attempts tracked.
                        </div>
                    </div>
                    <div class="p-0">
                        <button @click="open = open === 5 ? null : 5" class="w-full flex items-start justify-between gap-4 p-5 text-left">
                            <span class="font-medium text-[15px]">Will data migrate easily?</span>
                            <span class="w-7 h-7 rounded-full border border-stone-200 dark:border-stone-700 flex items-center justify-center shrink-0" x-text="open===5 ? '−' : '+'"></span>
                        </button>
                        <div x-show="open===5" x-collapse class="px-5 pb-5 text-[14px] leading-6 text-stone-600 dark:text-stone-400">
                            Master data routes cover levels, religions, job_positions, contract_types, education_types, family_types, relationships, document_types, leave_types, shifts. CSV import template aligns with existing columns.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-[#12372A] relative overflow-hidden">
        <div class="absolute inset-0 opacity-30 bg-[radial-gradient(50%_80%_at_20%_0%,#8fbf9f,transparent_60%),radial-gradient(40%_60%_at_90%_10%,#2a5a42,transparent_60%)]"></div>
        <div class="relative max-w-[1200px] mx-auto px-6 py-16 sm:py-20 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
            <div>
                <h2 class="text-[28px] sm:text-[38px] font-bold tracking-[-0.03em] leading-[1.05] text-white">Stop managing HR in 12 tabs.</h2>
                <p class="mt-3 text-[15px] leading-6 text-stone-200 max-w-xl">Landing maps 1:1 to routes/web.php. If it's on this page, it's already built. Unfinished parts are visible, not marketed.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center h-11 px-6 rounded-full bg-white text-[#12372A] text-[14px] font-semibold hover:bg-stone-100 transition-colors">Open workspace</a>
                <a href="#features" class="inline-flex items-center justify-center h-11 px-6 rounded-full bg-white/10 text-white border border-white/15 text-[14px] font-medium hover:bg-white/15 transition-colors">Explore modules</a>
            </div>
        </div>
    </section>

</div>
@endsection

@section('footer')
<footer class="bg-[#0E1210] text-stone-400 border-t border-stone-800/80">
    <div class="max-w-[1200px] mx-auto px-6 py-10 flex flex-col sm:flex-row items-start justify-between gap-8">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-white text-stone-900 flex items-center justify-center font-bold text-[13px]">T</div>
                <span class="text-white font-semibold tracking-tight">{{ config('app.name') }}</span>
            </div>
            <div class="mt-3 text-[13px] leading-6 max-w-sm">HRIS for ops teams. Attendance, shifts, leave, payroll, 360, tasks, facilities — built with Laravel 12.</div>
        </div>
        <div class="grid grid-cols-2 gap-10 text-[13px]">
            <div>
                <div class="text-[11px] tracking-widest uppercase font-semibold text-stone-500">Product</div>
                <ul class="mt-3 space-y-2">
                    <li><a href="#features" class="hover:text-white transition-colors">Modules</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign in</a></li>
                </ul>
            </div>
            <div>
                <div class="text-[11px] tracking-widest uppercase font-semibold text-stone-500">Meta</div>
                <ul class="mt-3 space-y-2">
                    <li><span class="opacity-80">Stack: Laravel 12 · Vite</span></li>
                    <li><span class="opacity-80">Auth: OTP · Throttle</span></li>
                    <li>© {{ date('Y') }} {{ config('app.name') }}</li>
                </ul>
            </div>
        </div>
    </div>
</footer>
@endsection
