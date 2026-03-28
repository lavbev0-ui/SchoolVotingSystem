<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $isVoter = auth('voter')->check() && !auth('web')->check();

        session()->forget('admin_2fa_verified');
        session()->forget('admin_2fa_code');

        $logout();

        if ($isVoter) {
            $this->redirect(route('voter.login'), navigate: true);
        } else {
            $this->redirect('/', navigate: true);
        }
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">

            {{-- Left: Logo + System Name --}}
            <div class="flex items-center gap-3">
                <div class="shrink-0 flex items-center">
                    @php
                        $isVoterOnly = auth('voter')->check() && !auth('web')->check();
                        $dashboardRoute = $isVoterOnly ? route('voter.dashboard') : route('admin.index');
                    @endphp
                    <a href="{{ $dashboardRoute }}" wire:navigate>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/school-logo.png') }}" alt="School Logo" class="w-14 h-14 object-contain">
                            <div class="leading-tight hidden sm:block">
                                <span class="block text-base font-black text-slate-800 uppercase tracking-tight">Enhance Voting System</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Catalino D. Cerezo NHS</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Right: Access Level + Dark Mode + Dropdown (Desktop) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <div class="text-right hidden lg:block">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Access Level</p>
                    <p class="text-[10px] font-black text-indigo-600 uppercase tracking-tighter">
                        {{ $isVoterOnly ? 'Active Voter' : 'System Administrator' }}
                    </p>
                </div>

                {{-- ── Dark Mode Toggle ── --}}
                <button onclick="toggleDark()"
                        title="Toggle Dark Mode"
                        class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-slate-500">
                    {{-- Moon icon (shown in light mode) --}}
                    <svg class="icon-moon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    {{-- Sun icon (shown in dark mode) --}}
                    <svg class="icon-sun w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.07-6.07l-.71.71M7.64 16.36l-.71.71M18.36 16.36l-.71.71M6.34 7.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-2xl text-gray-700 bg-white hover:bg-gray-50 transition ease-in-out duration-150 shadow-sm">
                            <div class="font-black uppercase text-[10px] tracking-tight">
                                {{ $isVoterOnly
                                    ? (auth('voter')->user()->first_name ?? 'User')
                                    : (auth()->user()->name ?? 'User') }}
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="$isVoterOnly ? route('voter.profile') : route('profile')" wire:navigate>
                            {{ __('My Profile') }}
                        </x-dropdown-link>
                        <hr class="border-gray-100">
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                <span class="text-red-600 font-black uppercase text-[10px]">{{ __('Sign Out') }}</span>
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center gap-2 sm:hidden">
                {{-- Dark Mode Toggle (Mobile) --}}
                <button onclick="toggleDark()"
                        title="Toggle Dark Mode"
                        class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-slate-500">
                    <svg class="icon-moon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    <svg class="icon-sun w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m8.66-9H21M3 12H2m15.07-6.07l-.71.71M7.64 16.36l-.71.71M18.36 16.36l-.71.71M6.34 7.34l-.71-.71M12 5a7 7 0 100 14A7 7 0 0012 5z"/>
                    </svg>
                </button>

                <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition duration-150">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-50 border-t border-slate-100">

        {{-- User Info + My Profile + Sign Out --}}
        <div class="px-4 pt-4 pb-3 border-b border-slate-100">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                {{ $isVoterOnly ? 'Active Voter' : 'System Administrator' }}
            </p>
            <p class="text-sm font-black text-slate-800 uppercase mb-3">
                {{ $isVoterOnly
                    ? (auth('voter')->user()->first_name ?? 'User')
                    : (auth()->user()->name ?? 'User') }}
            </p>
            <div class="flex items-center gap-2">
                <a href="{{ $isVoterOnly ? route('voter.profile') : route('profile') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">👤 My Profile</span>
                </a>
                <button wire:click="logout" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 rounded-xl transition-all">
                    <span class="text-red-600 font-black uppercase text-[10px] tracking-widest">🚪 Sign Out</span>
                </button>
            </div>
        </div>

        {{-- Admin Nav Links --}}
        @if(!$isVoterOnly)
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.index')" wire:navigate>📊 Analytics</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.elections.index')" :active="request()->routeIs('admin.elections.*')" wire:navigate>🗳️ Elections</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.voters.index')" :active="request()->routeIs('admin.voters.*')" wire:navigate>👥 Voters</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.results.index')" :active="request()->routeIs('admin.results.*')" wire:navigate>📈 Results</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" wire:navigate>⚙️ Settings</x-responsive-nav-link>
        </div>
        @endif

    </div>
</nav>