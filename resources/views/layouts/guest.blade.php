<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script>if(localStorage.getItem('darkMode')==='true'){document.documentElement.classList.add('dark');}</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important;}</style>
    @stack('head')
</head>
<body
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        init() {
            if (this.darkMode) document.documentElement.classList.add('dark');
            this.$watch('darkMode', (val) => {
                localStorage.setItem('darkMode', val);
                document.documentElement.classList.toggle('dark', val);
            });
        },
        toggleDark() { this.darkMode = !this.darkMode; }
    }"
    class="font-sans antialiased text-gray-900 bg-white dark:text-gray-100 dark:bg-gray-950 min-h-screen flex flex-col overflow-x-hidden"
>
    <x-toasts />

    @yield('content')

    <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-800 mt-auto">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>
