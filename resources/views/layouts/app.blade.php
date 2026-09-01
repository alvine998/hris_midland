<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>if(localStorage.getItem('darkMode')==='true'){document.documentElement.classList.add('dark');}</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important;}.page-fade{animation:pageFadeIn .18s ease both;}@keyframes pageFadeIn{from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:translateY(0);}}@media(max-width:1023px){[data-main-content]{padding-left:0!important;}}</style>
    @stack('head')
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:text-gray-100 dark:bg-gray-900 overflow-x-hidden">


    <x-toasts />

    <div
        x-data="{
            sidebarOpen: false,
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            darkMode: localStorage.getItem('darkMode') === 'true',
            userMenuOpen: false,
            notificationOpen: false,
            init() {
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                }
                this.$watch('darkMode', (val) => {
                    localStorage.setItem('darkMode', val);
                    document.documentElement.classList.toggle('dark', val);
                });
                const savedScroll = localStorage.getItem('sidebarScrollTop');
                if (savedScroll) {
                    this.$nextTick(() => { this.$refs.sidebarNav.scrollTop = parseInt(savedScroll, 10); });
                }
            },
            toggleDark() {
                this.darkMode = !this.darkMode;
            },
            toggleSidebar() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            }
        }"
        class="min-h-screen flex overflow-x-hidden"
    >
        {{-- Sidebar overlay for mobile --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-all duration-200 ease-in-out flex flex-col"
            :style="sidebarCollapsed ? 'width: 72px' : 'width: 256px'"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            {{-- Logo + Collapse toggle --}}
            <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight overflow-hidden whitespace-nowrap" :class="{ 'opacity-0 w-0': sidebarCollapsed }">
                    {{ config('app.name') }}
                </a>
                <button
                    @click="toggleSidebar()"
                    class="hidden lg:flex items-center justify-center w-8 h-8 ml-auto rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors shrink-0"
                    aria-label="Toggle sidebar"
                >
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav x-ref="sidebarNav" @scroll="localStorage.setItem('sidebarScrollTop', $el.scrollTop)" class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                @php
                    $isActive = fn(string ...$routes) => request()->routeIs(...$routes);
                    $activeClass = 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400';
                    $activeHover = 'hover:bg-indigo-50 dark:hover:bg-indigo-500/10';
                    $activeNested = 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400';
                    $activeNestedHover = 'hover:bg-indigo-50 dark:hover:bg-indigo-500/10';
                @endphp

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('dashboard') ? "$activeClass $activeHover" : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Dashboard</span>
                    <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Dashboard</div>
                </a>

                @if (Auth::user()?->isAdmin())
                <a href="{{ route('admin.overview') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('admin.overview') ? "$activeClass $activeHover" : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Admin Overview</span>
                    <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Admin Overview</div>
                </a>
                @endif

                {{-- AI Assistant --}}
                @if (Auth::user()?->isAdmin())
                <a href="{{ route('ai-chat.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('ai-chat.index') ? "$activeClass $activeHover" : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">AI Assistant</span>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" class="ml-auto rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300">AI</span>
                    <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">AI Assistant</div>
                </a>
                <a href="{{ route('ai-chat.knowledge.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('ai-chat.knowledge.*') ? "$activeClass $activeHover" : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">AI Knowledge Base</span>
                    <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">AI Knowledge Base</div>
                </a>
                @endif

                {{-- Recruitment --}}
                <a href="https://kerjaajadulu.com" target="_blank" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative group/item" :class="{ 'justify-center px-0': sidebarCollapsed }">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Recruitment</span>
                    <svg x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" class="w-3.5 h-3.5 ml-auto shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Recruitment</div>
                </a>

                {{-- Employees Group --}}
                <div x-data="{ open: {{ $isActive('employees.*') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_employees') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_employees', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('employees.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Employees</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Employees</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('employees.*') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span x-show="!sidebarCollapsed">All Employees</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">All Employees</div>
                        </a>
                    </div>
                </div>

                {{-- Tasks Group --}}
                <div x-data="{ open: {{ $isActive('employee-tasks.*') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_tasks') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_tasks', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('employee-tasks.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Tasks</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Tasks</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('employee-tasks.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('employee-tasks.*') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 8l2 2 4-4"/></svg>
                            <span x-show="!sidebarCollapsed">Employee Tasks</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Employee Tasks</div>
                        </a>
                    </div>
                </div>

                {{-- Leave Management Group --}}
                <div x-data="{ open: {{ $isActive('leave-requests.*', 'master-data.leave-types', 'leave-settings.*', 'leave-management.holidays.*') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_leave_management') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_leave_management', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('leave-requests.*', 'master-data.leave-types', 'leave-settings.*', 'leave-management.holidays.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Leave Management</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Leave Management</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('leave-requests.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('leave-requests.*') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H5v12z"/></svg>
                            <span x-show="!sidebarCollapsed">Leave Requests</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Leave Requests</div>
                        </a>
                        <a href="{{ route('master-data.leave-types') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.leave-types') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/></svg>
                            <span x-show="!sidebarCollapsed">Leave Types</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Leave Types</div>
                        </a>
                        <a href="{{ route('leave-settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('leave-settings.*') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M5 12h2m10 0h2M12 5v2m0 10v2"/></svg>
                            <span x-show="!sidebarCollapsed">Leave Settings</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Leave Settings</div>
                        </a>
                        <a href="{{ route('leave-management.holidays.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('leave-management.holidays.*') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H5v12z"/></svg>
                            <span x-show="!sidebarCollapsed">Holidays</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Holidays</div>
                        </a>
                    </div>
                </div>

                {{-- Payroll Group --}}
                <div x-data="{ open: {{ request()->is('*/payroll-periods') || request()->is('*/payrolls') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_payroll') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_payroll', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->is('*/payroll-periods') || request()->is('*/payrolls') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Payroll</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Payroll</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('admin-crud.index', 'payroll-periods') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/payroll-periods') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Payroll Periods</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Payroll Periods</div>
                        </a>
                        <a href="{{ route('admin-crud.index', 'payrolls') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/payrolls') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Payrolls</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Payrolls</div>
                        </a>
                    </div>
                </div>

                {{-- Reports Group --}}
                <div x-data="{ open: {{ $isActive('performance.report', 'attendances.index') || request()->is('*/activity-logs') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_reports') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_reports', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('performance.report', 'attendances.index') || request()->is('*/activity-logs') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Reports</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Reports</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('performance.report') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('performance.report') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span x-show="!sidebarCollapsed">Performance Report</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Performance Report</div>
                        </a>
                        <a href="{{ route('attendances.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('attendances.index') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-show="!sidebarCollapsed">Attendance Report</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Attendance Report</div>
                        </a>
                        <a href="{{ route('leave-requests.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('leave-requests.index') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H5v12z"/></svg>
                            <span x-show="!sidebarCollapsed">Leave Report</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Leave Report</div>
                        </a>
                        <a href="{{ route('admin-crud.index', 'activity-logs') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/activity-logs') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Activity Logs</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Activity Logs</div>
                        </a>
                    </div>
                </div>

                {{-- Performance Group --}}
                <div x-data="{ open: {{ request()->is('*/kpis') || $isActive('performance.feedback360.*') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_performance') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_performance', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->is('*/kpis') || $isActive('performance.feedback360.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Performance</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Performance</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('admin-crud.index', 'kpis') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/kpis') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                            <span x-show="!sidebarCollapsed">KPI</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">KPI</div>
                        </a>
                        <a href="{{ route('performance.feedback360.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('performance.feedback360.*') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v3l-4-3H9a2 2 0 01-2-2v-1m10-7V5a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v3l4-3h4a2 2 0 002-2V8z"/></svg>
                            <span x-show="!sidebarCollapsed">360 Feedback</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">360 Feedback</div>
                        </a>
                    </div>
                </div>

                {{-- Transfer Group --}}
                <div x-data="{ open: {{ request()->is('*/transfers') || request()->is('*/transfer-types') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_transfer') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_transfer', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->is('*/transfers') || request()->is('*/transfer-types') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Transfer</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Transfer</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('admin-crud.index', 'transfers') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/transfers') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Transfers</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Transfers</div>
                        </a>
                        <a href="{{ route('admin-crud.index', 'transfer-types') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/transfer-types') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Transfer Types</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Transfer Types</div>
                        </a>
                    </div>
                </div>

                {{-- Facilities Group --}}
                <div x-data="{ open: {{ request()->is('*/facilities') || request()->is('*/facility-criterias') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_facilities') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_facilities', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->is('*/facilities') || request()->is('*/facility-criterias') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Facilities</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Facilities</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('admin-crud.index', 'facilities') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/facilities') && !request()->is('*/facility-criterias') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Facilities</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Facilities</div>
                        </a>
                        <a href="{{ route('admin-crud.index', 'facility-criterias') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ request()->is('*/facility-criterias') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Facility Criteria</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Facility Criteria</div>
                        </a>
                    </div>
                </div>

                {{-- Communication Group --}}
                <!-- <div x-data="{ open: localStorage.getItem('sidebar_group_communication') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_communication', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <span>Communication</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('admin-crud.index', 'notifications') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">Notifications</a>
                        <a href="{{ route('communication.chats.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">Chats</a>
                    </div>
                </div> -->

                {{-- Attendance Group --}}
                <div x-data="{ open: {{ $isActive('attendances.*') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_attendance') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_attendance', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('attendances.*') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Attendance</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Attendance</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('attendances.check-in') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('attendances.check-in') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-show="!sidebarCollapsed">Check In / Out</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Check In / Out</div>
                        </a>
                        <a href="{{ route('attendances.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('attendances.index') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Attendances</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Attendances</div>
                        </a>
                    </div>
                </div>

                {{-- Organization Group --}}
                <div x-data="{ open: {{ $isActive('master-data.companies', 'master-data.departments', 'master-data.divisions', 'master-data.sections', 'master-data.work-locations') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_organization') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_organization', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('master-data.companies', 'master-data.departments', 'master-data.divisions', 'master-data.sections', 'master-data.work-locations') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Organization</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Organization</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('master-data.companies') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.companies') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-show="!sidebarCollapsed">Companies</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Companies</div>
                        </a>
                        <a href="{{ route('master-data.departments') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-show="!sidebarCollapsed">Departments</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Departments</div>
                        </a>
                        <a href="{{ route('master-data.divisions') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-show="!sidebarCollapsed">Divisions</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Divisions</div>
                        </a>
                        <a href="{{ route('master-data.sections') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-show="!sidebarCollapsed">Sections</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Sections</div>
                        </a>
                        <a href="{{ route('master-data.work-locations') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Work Locations</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Work Locations</div>
                        </a>
                    </div>
                </div>

                {{-- Reference Group --}}
                <div x-data="{ open: {{ $isActive('master-data.levels', 'master-data.job-positions', 'master-data.religions', 'master-data.contract-types', 'master-data.education-types', 'master-data.family-types', 'master-data.relationships', 'master-data.document-types', 'master-data.shifts', 'master-data.approval-workflows', 'master-data.roles', 'master-data.modules') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_reference') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_reference', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('master-data.levels', 'master-data.job-positions', 'master-data.religions', 'master-data.contract-types', 'master-data.education-types', 'master-data.family-types', 'master-data.relationships', 'master-data.document-types', 'master-data.shifts', 'master-data.approval-workflows', 'master-data.roles', 'master-data.modules') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Reference</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Reference</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('master-data.levels') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span x-show="!sidebarCollapsed">Levels</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Levels</div>
                        </a>
                        <a href="{{ route('master-data.job-positions') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span x-show="!sidebarCollapsed">Job Positions</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Job Positions</div>
                        </a>
                        <a href="{{ route('master-data.religions') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 3l7 7-7 7-7-7 7-7z"/></svg>
                            <span x-show="!sidebarCollapsed">Religions</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Religions</div>
                        </a>
                        <a href="{{ route('master-data.contract-types') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-show="!sidebarCollapsed">Contract Types</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Contract Types</div>
                        </a>
                        <a href="{{ route('master-data.education-types') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            <span x-show="!sidebarCollapsed">Education Types</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Education Types</div>
                        </a>
                        <a href="{{ route('master-data.family-types') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Family Types</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Family Types</div>
                        </a>
                        <a href="{{ route('master-data.relationships') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
                            <span x-show="!sidebarCollapsed">Relationships</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Relationships</div>
                        </a>
                        <a href="{{ route('master-data.document-types') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/></svg>
                            <span x-show="!sidebarCollapsed">Document Types</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Document Types</div>
                        </a>
                        <a href="{{ route('master-data.shifts') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Shifts</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Shifts</div>
                        </a>
                        <a href="{{ route('master-data.approval-workflows') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Approval Workflows</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Approval Workflows</div>
                        </a>
                        <a href="{{ route('master-data.roles') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Roles</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Roles</div>
                        </a>
                        <a href="{{ route('master-data.modules') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span x-show="!sidebarCollapsed">Modules</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Modules</div>
                        </a>
                    </div>
                </div>

                {{-- Administration Group --}}
                <div x-data="{ open: {{ $isActive('user-roles.*', 'settings.*') || request()->is('*/activity-logs') || request()->is('*/login-attempts') ? 'true' : 'false' }} || localStorage.getItem('sidebar_group_administration') === 'true' }">
                    <button @click="open = !open; localStorage.setItem('sidebar_group_administration', open)" class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold uppercase tracking-wider transition-colors {{ $isActive('user-roles.*', 'settings.*') || request()->is('*/activity-logs') || request()->is('*/login-attempts') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}" :class="{ 'justify-center px-0 uppercase-none': sidebarCollapsed }">
                        <span x-show="!sidebarCollapsed">Administration</span>
                        <svg x-show="!sidebarCollapsed" class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Administration</div>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <a href="{{ route('user-roles.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span x-show="!sidebarCollapsed">User Roles</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">User Roles</div>
                        </a>
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-show="!sidebarCollapsed">Settings</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Settings</div>
                        </a>
                        <a href="{{ route('settings.documentation') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                            <span x-show="!sidebarCollapsed">Documentation</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Documentation</div>
                        </a>
                        <a href="{{ route('admin-crud.index', 'activity-logs') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Activity Logs</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Activity Logs</div>
                        </a>
                        <a href="{{ route('admin-crud.index', 'login-attempts') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors relative group/item {{ $isActive('master-data.departments') ? "$activeNested $activeNestedHover" : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100' }}" :class="{ 'justify-center px-0': sidebarCollapsed }">
                            <span x-show="!sidebarCollapsed">Login Attempts</span>
                            <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Login Attempts</div>
                        </a>
                    </div>
                </div>
            </nav>

            {{-- Sidebar footer --}}
            <div class="px-3 py-3 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                    @csrf
                </form>
                <button
                    onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"
                    class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-red-600 dark:hover:text-red-400 transition-colors relative group/item"
                    :class="{ 'justify-center px-0': sidebarCollapsed }"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-opacity duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Logout</span>
                    <div x-show="sidebarCollapsed" x-cloak class="absolute left-full ml-2 px-2 py-1 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium whitespace-nowrap opacity-0 group-hover/item:opacity-100 pointer-events-none transition-opacity z-50">Logout</div>
                </button>
            </div>
        </aside>

        {{-- Main content area --}}
        <div data-main-content class="flex-1 flex flex-col min-h-screen min-w-0 transition-all duration-200" :style="'padding-left: ' + (sidebarCollapsed ? '72px' : '256px')">
            {{-- Top navbar --}}
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-6 shrink-0 sticky top-0 z-30">
                {{-- Left: hamburger + page title --}}
                <div class="flex items-center gap-3">
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 -ml-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Toggle sidebar"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 hidden sm:block">
                        @yield('title', 'Dashboard')
                    </h1>
                </div>

                {{-- Right: dark mode toggle + user dropdown --}}
                <div class="flex items-center gap-3">
                    {{-- Dark mode toggle --}}
                    <button
                        @click="toggleDark()"
                        class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Toggle dark mode"
                    >
                        <svg x-cloak x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-cloak x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    <div class="relative" @click.outside="notificationOpen = false">
                        <button
                            type="button"
                            @click="notificationOpen = !notificationOpen; userMenuOpen = false"
                            class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            aria-label="Open notifications"
                            title="Notifications"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
                            </svg>
                            @php($unreadCount = $globalNotifications->where('is_read', false)->count())
                            @if ($unreadCount > 0)
                                <span class="absolute -right-1 -top-1 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white dark:ring-gray-800">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @endif
                        </button>

                        <div
                            x-show="notificationOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
                        >
                            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                                <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</p>
                            </div>

                            <div class="max-h-80 overflow-y-auto py-1">
                                @forelse ($globalNotifications as $notification)
                                    <div class="border-b border-gray-100 px-4 py-3 last:border-0 dark:border-gray-700/70">
                                        <div class="flex items-start gap-3">
                                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full {{ $notification->is_read ? 'bg-gray-300 dark:bg-gray-600' : 'bg-indigo-500' }}"></span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ $notification->title }}</p>
                                                @if ($notification->message)
                                                    <p class="mt-0.5 line-clamp-2 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $notification->message }}</p>
                                                @endif
                                                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">{{ $notification->created_at?->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No notifications yet.
                                    </div>
                                @endforelse
                            </div>

                            <a href="{{ route('admin-crud.index', 'notifications') }}" class="block border-t border-gray-200 px-4 py-3 text-center text-sm font-medium text-indigo-600 hover:bg-gray-50 dark:border-gray-700 dark:text-indigo-400 dark:hover:bg-gray-700/60">
                                View All
                            </a>
                        </div>
                    </div>

                    {{-- User dropdown --}}
                    <div class="relative" @click.outside="userMenuOpen = false">
                        <button
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 p-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            <span class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded-full flex items-center justify-center text-sm font-semibold">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </span>
                            <span class="hidden md:block">{{ Auth::user()->name ?? 'User' }}</span>
                            <svg class="hidden md:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Dropdown menu --}}
                        <div
                            x-show="userMenuOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2"
                        >
                            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profile
                            </a>
                            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </a>
                            <hr class="my-1 border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}" id="dropdown-logout-form">
                                @csrf
                            </form>
                            <button
                                onclick="event.preventDefault(); document.getElementById('dropdown-logout-form').submit();"
                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main content --}}
            <main class="flex-1 min-w-0 max-w-full overflow-x-hidden p-4 lg:p-6 page-fade">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="px-6 py-4 text-center text-xs text-gray-400 dark:text-gray-500 border-t border-gray-200 dark:border-gray-800">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>

        <x-chat-widget :chats="$globalChats" :users="$globalChatUsers" />
    </div>

    @stack('scripts')
</body>
</html>
