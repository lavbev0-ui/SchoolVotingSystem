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
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            
            <livewire:layout.navigation />

            {{-- 
                ADMIN SUB-NAVIGATION
                Logic:
                1. Must be on a 'dashboard.*' route.
                2. User must be logged in.
                3. User must NOT have a student_id (Admins use Email, Voters use Student ID).
            --}}
            @if (request()->routeIs('dashboard.*') && auth()->check() && !auth()->user()->student_id)
            <div class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8">
                <div class="grid w-full grid-cols-2 md:grid-cols-5 gap-1 rounded-xl bg-gray-200/50 p-1">
                    
                    <a href="{{ route('dashboard.index') }}" 
                       class="{{ request()->routeIs('dashboard.index') ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-900' }} flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-all"
                       wire:navigate>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        <span>Overview</span>
                    </a>

                    <a href="{{ route('dashboard.elections.index') }}" 
                       class="{{ request()->routeIs('dashboard.elections.*') ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-900' }} flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-all"
                       wire:navigate>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Elections</span>
                    </a>

                    <a href="{{ route('dashboard.voters.index') }}" 
                       class="{{ request()->routeIs('dashboard.voters.*') ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-900' }} flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-all"
                       wire:navigate>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span>Voters</span>
                    </a>

                    <a href="{{ route('dashboard.results.index') }}" 
                       class="{{ request()->routeIs('dashboard.results.*') ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-900' }} flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-all">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        <span>Results</span>
                    </a>

                    <a href="{{ route('dashboard.settings.index') }}" 
                       class="{{ request()->routeIs('dashboard.settings.*') ? 'bg-white text-gray-950 shadow-sm' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-900' }} flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-all">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
            @endif

            
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>