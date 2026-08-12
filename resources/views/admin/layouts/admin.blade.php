<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important;}.page-fade{animation:pageFadeIn .18s ease both;}@keyframes pageFadeIn{from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:translateY(0);}}</style>
    @stack('head')
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:text-gray-100 dark:bg-gray-900 overflow-x-hidden">

    <x-toasts />

    <div
        x-data="{ sidebarOpen: false, userMenuOpen: false }"
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
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            <div class="flex items-center h-16 px-6 border-b border-gray-200 dark:border-gray-700 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                    {{ config('app.name') }} <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Admin</span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Customers
                </a>

                <a href="{{ route('admin.packages.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.packages.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20a8 8 0 100-16 8 8 0 000 16zM16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Packages
                </a>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Orders
                </a>

                <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.faqs.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    FAQs
                </a>

                <a href="{{ route('admin.tickets.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.tickets.*') ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2h7m-4 3h9m-9 3h9"/></svg>
                    Tickets
                </a>
            </nav>

            {{-- Sidebar footer --}}
            <div class="px-3 py-3 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('admin.logout') }}" id="sidebar-logout-form">
                    @csrf
                </form>
                <button
                    onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"
                    class="flex items-center gap-3 w-full px-3 py-2.5 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        {{-- Main content area --}}
        <div class="flex-1 flex flex-col min-h-screen min-w-0 lg:pl-64">
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 lg:px-6 shrink-0 sticky top-0 z-30">
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

                <div class="relative" @click.outside="userMenuOpen = false">
                    <button
                        @click="userMenuOpen = !userMenuOpen"
                        class="flex items-center gap-2 p-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    >
                        <span class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded-full flex items-center justify-center text-sm font-semibold">
                            {{ substr(Auth::guard('admin')->user()->name ?? 'A', 0, 1) }}
                        </span>
                        <span class="hidden md:block">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                        <svg class="hidden md:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

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
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ Auth::guard('admin')->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::guard('admin')->user()->email }}</p>
                        </div>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <form method="POST" action="{{ route('admin.logout') }}" id="dropdown-logout-form">
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
            </header>

            <main class="flex-1 min-w-0 max-w-full overflow-x-hidden p-4 lg:p-6 page-fade">
                @yield('content')
            </main>

            <footer class="px-6 py-4 text-center text-xs text-gray-400 dark:text-gray-500 border-t border-gray-200 dark:border-gray-800">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>
    </div>

    @stack('scripts')
    <script>
    (() => {
        const fmt = v => {
            const d = v.replace(/\D/g, '');
            if (d === '') return '';
            // strip leading zeros but keep single 0
            const n = d.replace(/^0+(?=\d)/, '');
            return n.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };
        const strip = v => v.replace(/\./g, '');
        const attach = el => {
            if (el.dataset.thousand) return;
            el.dataset.thousand = '1';
            el.type = 'text';
            el.inputMode = 'numeric';
            el.autocomplete = 'off';
            if (el.value) el.value = fmt(el.value);
            el.addEventListener('input', () => {
                const pos = el.selectionStart ?? el.value.length;
                const before = el.value.slice(0, pos);
                const digitsBefore = before.replace(/\D/g, '').length;
                el.value = fmt(el.value);
                let seen = 0, newPos = el.value.length;
                if (digitsBefore === 0) newPos = 0;
                else {
                    for (let i = 0; i < el.value.length; i++) {
                        if (/\d/.test(el.value[i])) seen++;
                        if (seen === digitsBefore) { newPos = i + 1; break; }
                    }
                }
                el.setSelectionRange(newPos, newPos);
            });
            // paste: let input handler format it
            el.addEventListener('blur', () => { if (el.value) el.value = fmt(el.value); });
        };
        const init = () => document.querySelectorAll('input[type="number"]').forEach(attach);
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
        else init();
        // handle dynamically added (e.g. after Turbo/Alpine)
        new MutationObserver(init).observe(document.body, { childList: true, subtree: true });
        document.addEventListener('submit', e => {
            const form = e.target;
            if (form.tagName !== 'FORM') return;
            form.querySelectorAll('[data-thousand="1"]').forEach(inp => { inp.value = strip(inp.value); });
        }, true);
    })();
    </script>
</body>
</html>
