<nav class="w-full border-b bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">

            <!-- Left: Logo + System Name -->
            <div class="flex items-center gap-3">
                <img
                    src="{{ asset('images/school-logo.png') }}"
                    alt="Catalino D. Cerezo National High School Logo"
                    class="w-12 h-12 object-contain"
                />
                <div class="leading-tight">
                    <span class="block text-lg font-semibold">
                        Enhance Voting System
                    </span>
                    <span class="text-sm text-gray-500">
                        CATALINO D. CEREZO NHS
                    </span>
                </div>
            </div>

            <!-- Right: User Info + Logout -->
            <div class="flex items-center gap-4">

                <!-- Student Name -->
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-medium text-gray-800">
                        {{ auth('voter')->user()->name ?? 'Student' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Voter Account
                    </p>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('voter.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2
                               border rounded-md text-sm font-medium
                               text-gray-700 hover:bg-gray-100
                               transition"
                    >
                        <!-- Logout Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                        </svg>
                        Logout
                    </button>
                </form>

            </div>
        </div>
    </div>
</nav>
