<nav class="w-full bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('voter.dashboard') }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/school-logo.png') }}" class="w-12 h-12 object-contain"/>
                        <div class="leading-tight hidden sm:block">
                            <span class="block text-lg font-semibold text-slate-800">Enhance Voting System</span>
                            <span class="text-sm text-gray-500">CATALINO D. CEREZO NHS</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">

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

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 transition cursor-pointer">
                        <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-slate-200 shrink-0">
                            @if(auth('voter')->user()->photo_path)
                                <img src="{{ asset('storage/' . auth('voter')->user()->photo_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-sm uppercase">
                                    {{ substr(auth('voter')->user()->first_name ?? 'S', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-medium text-gray-800 leading-none">
                                {{ auth('voter')->user()->first_name ?? '' }} {{ auth('voter')->user()->last_name ?? 'Student' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">Voter Account</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform hidden sm:block"
                             :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 overflow-hidden">
                        <a href="{{ route('voter.profile') }}"
                           class="flex items-center gap-3 px-5 py-3.5 text-sm font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            My Profile
                        </a>
                        <div class="border-t border-slate-100"></div>
                        <form method="POST" action="{{ route('voter.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-5 py-3.5 text-sm font-bold text-red-500 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>