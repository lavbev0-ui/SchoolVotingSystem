<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Enhance Voting System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-gray-50 text-gray-900 min-h-screen flex flex-col">

<!-- ================= HEADER ================= -->
<header class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        <!-- Logo + System Name -->
        <div class="flex items-center gap-3">
            <img
                src="{{ asset('images/school-logo.png') }}"
                alt="Catalino D. Cerezo National High School Logo"
                class="w-12 h-12 object-contain"
            >
            <span class="text-xl font-semibold text-blue-900">
                Enhance Voting System
            </span>
        </div>

        <!-- Back to Home -->
        <a href="{{ url('/') }}"
           class="text-sm font-medium text-blue-900 hover:underline">
            ← Back to Home
        </a>

    </div>
</header>

<!-- ================= MAIN ================= -->
<main class="flex-1 flex items-center justify-center px-4 py-12">
    {{ $slot }}
</main>

<!-- ================= FOOTER ================= -->
<footer class="bg-white border-t border-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-6 py-8 text-center">
        <p class="font-semibold text-sm md:text-base">
            CATALINO D. CEREZO NATIONAL HIGH SCHOOL
        </p>
        <p class="text-sm text-gray-500 mt-1">
            © {{ date('Y') }} Enhance Voting System. All rights reserved.
        </p>
    </div>
</footer>

</body>
</html>
