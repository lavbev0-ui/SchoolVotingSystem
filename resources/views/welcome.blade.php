<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Enhance Voting System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans antialiased bg-white text-gray-900 min-h-screen flex flex-col">

<!-- ================= HEADER ================= -->
<header class="w-full border-b border-yellow-400">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        <!-- Logo + System Name -->
        <div class="flex items-center gap-3">
            <img
                src="{{ asset('images/school-logo.png') }}"
                alt="School Logo"
                class="w-12 h-12 object-contain"
            >
            <span class="text-xl font-semibold text-blue-900">
                Enhance Voting System
            </span>
        </div>

        <!-- Navigation -->
        <nav class="flex items-center gap-4">
            @auth('web')
                <a href="{{ route('dashboard.index') }}"
                   class="px-6 py-2 rounded-lg border border-blue-600 text-blue-600 font-medium hover:bg-blue-600 hover:text-white transition">
                    Admin Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-6 py-2 rounded-lg border border-blue-600 text-blue-600 font-medium hover:bg-blue-600 hover:text-white transition">
                    Admin Login
                </a>
            @endauth
        </nav>

    </div>
</header>

<!-- ================= MAIN ================= -->
<main class="flex-1">

    <!-- ================= HERO ================= -->
    <section class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Left Content -->
            <div class="flex flex-col gap-6 order-2 lg:order-1">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight text-blue-900">
                    Secure Digital Voting<br>
                    Made Simple
                </h1>

                <p class="text-lg text-gray-600 max-w-xl">
                    Empowering students of Catalino D. Cerezo National High School
                    with a modern, transparent, and secure voting platform.
                </p>

                <div class="flex gap-4 mt-2">
                    @if(Auth::guard('voter')->check())
                        <a href="{{ route('voter.dashboard') }}"
                           class="px-8 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                            Vote Now
                        </a>
                    @else
                        <a href="{{ route('voter.login') }}"
                           class="px-8 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                            Vote Now
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Image -->
            <div class="order-1 lg:order-2">
                <div class="rounded-2xl overflow-hidden shadow-xl border border-yellow-400/40">
                    <img
                        src="https://images.unsplash.com/photo-1663512847531-7e1a37f5f4b1?fit=crop&w=1200&q=80"
                        alt="Students voting"
                        class="w-full h-auto object-cover aspect-[4/3]"
                    >
                </div>
            </div>

        </div>
    </section>

    <!-- ================= FEATURES ================= -->
    <section class="py-16 bg-yellow-50">
        <div class="max-w-7xl mx-auto px-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Feature 1 -->
                <div class="p-6 bg-white rounded-xl border border-yellow-400/40 hover:shadow-md transition">
                    <h3 class="text-xl font-semibold text-blue-900 mb-2">
                        Secure & Private
                    </h3>
                    <p class="text-gray-600">
                        Votes are encrypted and anonymous to maintain election integrity
                        and student privacy.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 bg-white rounded-xl border border-yellow-400/40 hover:shadow-md transition">
                    <h3 class="text-xl font-semibold text-blue-900 mb-2">
                        Easy to Use
                    </h3>
                    <p class="text-gray-600">
                        Student-friendly interface designed for fast and easy voting
                        on any device.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 bg-white rounded-xl border border-yellow-400/40 hover:shadow-md transition">
                    <h3 class="text-xl font-semibold text-blue-900 mb-2">
                        Real-time Results
                    </h3>
                    <p class="text-gray-600">
                        Transparent vote counting with real-time monitoring
                        for fair and trusted school elections.
                    </p>
                </div>

            </div>
        </div>
    </section>

</main>

<!-- ================= FOOTER ================= -->
<footer class="border-t border-yellow-400">
    <div class="max-w-7xl mx-auto px-6 py-8 text-center">
        <p class="font-semibold text-blue-900">
            CATALINO D. CEREZO NATIONAL HIGH SCHOOL
        </p>
        <p class="text-sm text-gray-500 mt-1">
            © {{ date('Y') }} Enhance Voting System. All rights reserved.
        </p>
    </div>
</footer>

</body>
</html>
