<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @media print { .no-print { display: none !important; } }
            .scrollbar-none::-webkit-scrollbar { display: none; }
            .scrollbar-none { scrollbar-width: none; -ms-overflow-style: none; }

            /* ================================================================
               DARK MODE — Proper approach, walang filter:invert
               Kaya hindi nagbabago ang kulay ng candidate photos
               ================================================================ */

            /* Base */
            html.dark-mode body {
                background-color: #0f1117 !important;
                color: #e2e8f0 !important;
            }
            html.dark-mode .min-h-screen {
                background-color: #0f1117 !important;
            }

            /* Backgrounds */
            html.dark-mode .bg-white        { background-color: #1e2130 !important; }
            html.dark-mode .bg-gray-100     { background-color: #161923 !important; }
            html.dark-mode .bg-gray-50      { background-color: #0f1117 !important; }
            html.dark-mode .bg-slate-50     { background-color: #1a1f2e !important; }
            html.dark-mode .bg-slate-100    { background-color: #252a3a !important; }
            html.dark-mode .bg-slate-900    { background-color: #060810 !important; }

            /* Text */
            html.dark-mode .text-slate-900  { color: #f1f5f9 !important; }
            html.dark-mode .text-slate-800  { color: #e2e8f0 !important; }
            html.dark-mode .text-slate-700  { color: #cbd5e1 !important; }
            html.dark-mode .text-slate-500  { color: #94a3b8 !important; }
            html.dark-mode .text-slate-400  { color: #64748b !important; }

            /* Borders */
            html.dark-mode .border-gray-200 { border-color: #2d3347 !important; }
            html.dark-mode .border-slate-100{ border-color: #2d3347 !important; }
            html.dark-mode .border-slate-200{ border-color: #374151 !important; }

            /* Admin Navbar + Sub-nav */
            html.dark-mode .sticky.top-0,
            html.dark-mode .sticky.top-0 .bg-white {
                background-color: #12151f !important;
            }
            html.dark-mode .border-b.border-gray-200.shadow-sm {
                background-color: #12151f !important;
                border-color: #2d3347 !important;
            }

            /* Admin sub-nav links */
            html.dark-mode .text-slate-500.hover\:text-slate-800 {
                color: #94a3b8 !important;
            }
            html.dark-mode .hover\:border-slate-300:hover {
                border-color: #475569 !important;
            }

            /* Header */
            html.dark-mode header.bg-white.shadow {
                background-color: #1e2130 !important;
                box-shadow: 0 1px 3px rgba(0,0,0,0.4) !important;
            }

            /* Inputs */
            html.dark-mode input,
            html.dark-mode textarea,
            html.dark-mode select {
                background-color: #252a3a !important;
                color: #e2e8f0 !important;
                border-color: #374151 !important;
            }

            /* Tables */
            html.dark-mode table          { color: #e2e8f0 !important; }
            html.dark-mode th             { color: #94a3b8 !important; background-color: #1a1f2e !important; }
            html.dark-mode td             { border-color: #2d3347 !important; }
            html.dark-mode tr:hover td    { background-color: #252a3a !important; }

            /* Modals / Cards */
            html.dark-mode .shadow,
            html.dark-mode .shadow-sm,
            html.dark-mode .shadow-lg {
                box-shadow: 0 4px 24px rgba(0,0,0,0.5) !important;
            }

            /* Scrollbar */
            html.dark-mode ::-webkit-scrollbar-track { background: #1a1f2e; }
            html.dark-mode ::-webkit-scrollbar-thumb { background: #374151; border-radius: 8px; }

            /* ================================================================
               PHOTOS / IMAGES — HINDI NAGBABAGO NG KULAY SA DARK MODE
               ================================================================ */
            html.dark-mode img {
                filter: none !important;
            }
            /* Panatilihin ang grayscale kung may class na ganoon */
            html.dark-mode img.grayscale {
                filter: grayscale(100%) !important;
            }
            html.dark-mode .group:hover img.grayscale {
                filter: none !important;
            }
        </style>

        {{-- Apply dark mode instantly — no flash --}}
        <script>
            if (localStorage.getItem('adminDarkMode') === 'true') {
                document.documentElement.classList.add('dark-mode');
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">

            {{-- STICKY NAVBAR WRAPPER --}}
            <div class="sticky top-0 z-50">
                <livewire:layout.navigation />

                {{-- Admin sub-nav --}}
                @if(request()->routeIs('admin.*') && auth()->guard('web')->check())
                <div class="bg-white border-b border-gray-200 shadow-sm no-print">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between gap-1 overflow-x-auto scrollbar-none w-full">

                            <a href="{{ route('admin.index') }}"
                               class="flex items-center gap-2 px-4 py-4 text-[11px] font-black uppercase tracking-widest border-b-2 transition-all whitespace-nowrap shrink-0
                               {{ request()->routeIs('admin.index') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                DASHBOARD
                            </a>

                            <a href="{{ route('admin.elections.index') }}"
                               class="flex items-center gap-2 px-4 py-4 text-[11px] font-black uppercase tracking-widest border-b-2 transition-all whitespace-nowrap shrink-0
                               {{ request()->routeIs('admin.elections.*') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Elections
                            </a>

                            <a href="{{ route('admin.voters.index') }}"
                               class="flex items-center gap-2 px-4 py-4 text-[11px] font-black uppercase tracking-widest border-b-2 transition-all whitespace-nowrap shrink-0
                               {{ request()->routeIs('admin.voters.*') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Voters
                            </a>

                            <a href="{{ route('admin.results.index') }}"
                               class="flex items-center gap-2 px-4 py-4 text-[11px] font-black uppercase tracking-widest border-b-2 transition-all whitespace-nowrap shrink-0
                               {{ request()->routeIs('admin.results.*') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                Results
                            </a>

                            <a href="{{ route('admin.settings.index') }}"
                               class="flex items-center gap-2 px-4 py-4 text-[11px] font-black uppercase tracking-widest border-b-2 transition-all whitespace-nowrap shrink-0
                               {{ request()->routeIs('admin.settings.*') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Settings
                            </a>

                        </div>
                    </div>
                </div>
                @endif
            </div>

            @if (isset($header))
                <header class="bg-white shadow no-print">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Dark Mode Toggle Script --}}
        <script>
            function toggleDark() {
                const isDark = document.documentElement.classList.toggle('dark-mode');
                localStorage.setItem('adminDarkMode', isDark);
                document.querySelectorAll('.icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
                document.querySelectorAll('.icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
            }
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = localStorage.getItem('adminDarkMode') === 'true';
                document.querySelectorAll('.icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
                document.querySelectorAll('.icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
            });
        </script>
    </body>
</html>