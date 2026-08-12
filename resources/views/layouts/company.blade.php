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
    @stack('head')
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 dark:text-gray-100 dark:bg-gray-900 min-h-screen flex flex-col">
    <x-toasts />

    @yield('content')

    <footer class="py-6 text-center text-sm text-stone-500 dark:text-stone-400 border-t border-stone-200 dark:border-stone-800 mt-auto">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>
