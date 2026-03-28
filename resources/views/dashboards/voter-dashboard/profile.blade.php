<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile | Enhance Voting System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }

        /* ================================================================
           DARK MODE — My Profile Page
           ================================================================ */

        html.dark-mode body {
            background-color: #0f1117 !important;
            color: #e2e8f0 !important;
        }

        /* Backgrounds */
        html.dark-mode .bg-white          { background-color: #1e2130 !important; }
        html.dark-mode .bg-gray-50        { background-color: #0f1117 !important; }
        html.dark-mode .bg-indigo-50      { background-color: #1e1a3a !important; }
        html.dark-mode .bg-emerald-50     { background-color: #052e16 !important; }

        /* Text */
        html.dark-mode .text-slate-900    { color: #f1f5f9 !important; }
        html.dark-mode .text-slate-800    { color: #e2e8f0 !important; }
        html.dark-mode .text-slate-700    { color: #cbd5e1 !important; }
        html.dark-mode .text-slate-500    { color: #94a3b8 !important; }
        html.dark-mode .text-slate-400    { color: #64748b !important; }
        html.dark-mode .text-indigo-400   { color: #a5b4fc !important; }
        html.dark-mode .text-emerald-700  { color: #34d399 !important; }

        /* Borders */
        html.dark-mode .border-slate-100  { border-color: #2d3347 !important; }
        html.dark-mode .border-slate-200  { border-color: #374151 !important; }
        html.dark-mode .border-indigo-100 { border-color: #312e6e !important; }
        html.dark-mode .border-emerald-200{ border-color: #065f46 !important; }

        /* Inputs */
        html.dark-mode input[type="text"],
        html.dark-mode input[type="email"],
        html.dark-mode input[type="password"] {
            background-color: #252a3a !important;
            border-color: #374151 !important;
            color: #e2e8f0 !important;
        }
        html.dark-mode input::placeholder {
            color: #64748b !important;
        }
        html.dark-mode input:focus {
            ring-color: #6366f1 !important;
            border-color: #6366f1 !important;
        }

        /* Cards */
        html.dark-mode .rounded-\[2rem\] {
            background-color: #1e2130 !important;
            border-color: #2d3347 !important;
        }

        /* Navbar */
        html.dark-mode nav { background-color: #12151f !important; }

        /* Back button */
        html.dark-mode .bg-white.border.border-slate-200 {
            background-color: #252a3a !important;
            border-color: #374151 !important;
        }

        /* Images */
        html.dark-mode img { filter: none !important; }
    </style>

    {{-- Apply dark mode instantly (no flash) --}}
    <script>
        if (localStorage.getItem('voterDarkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 font-sans antialiased">

    @include('dashboards.voter-dashboard.layout.nav')

    <main class="flex-1 py-10">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER --}}
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('voter.dashboard') }}"
                   class="flex items-center gap-2 text-slate-400 hover:text-indigo-600 transition group shrink-0">
                    <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:bg-indigo-50 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest hidden sm:block">Back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-black uppercase text-slate-900 tracking-tight">My Profile</h1>
                    <p class="text-slate-400 font-bold uppercase text-[9px] tracking-widest mt-0.5">Manage your account</p>
                </div>
            </div>

            {{-- SUCCESS TOAST --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 x-init="setTimeout(() => show = false, 4000)"
                 class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-2xl text-sm font-bold">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- VOTER INFO CARD --}}
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="bg-indigo-50 px-8 py-5 border-b border-indigo-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl overflow-hidden border-2 border-white shadow shrink-0">
                        @if($voter->photo_path)
                            <img src="{{ asset('storage/' . $voter->photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-xl uppercase">
                                {{ substr($voter->first_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-800 uppercase">{{ $voter->full_name }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                            {{ $voter->gradeLevel?->name ?? '—' }} — {{ $voter->section?->name ?? '—' }}
                        </p>
                        <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest mt-0.5">
                            Student ID: {{ $voter->student_id }}
                        </p>
                    </div>
                </div>

                {{-- PROFILE INFO FORM --}}
                <form method="POST" action="{{ route('voter.profile.update-info') }}" class="p-8 space-y-5">
                    @csrf
                    @method('PATCH')

                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3 mb-5">
                        Profile Information
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">First Name</label>
                            <input type="text" name="first_name"
                                   value="{{ old('first_name', $voter->first_name) }}"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('first_name') border-red-400 @enderror">
                            @error('first_name')
                                <p class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Last Name</label>
                            <input type="text" name="last_name"
                                   value="{{ old('last_name', $voter->last_name) }}"
                                   class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('last_name') border-red-400 @enderror">
                            @error('last_name')
                                <p class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Email Address</label>
                        <input type="email" name="email"
                               value="{{ old('email', $voter->email) }}"
                               placeholder="Optional"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-700 transition shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- UPDATE PASSWORD CARD --}}
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-8 space-y-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3 mb-5">
                    Update Password
                </p>

                <form method="POST" action="{{ route('voter.profile.update-password') }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Current Password</label>
                        <input type="password" name="current_password"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('current_password') border-red-400 @enderror">
                        @error('current_password')
                            <p class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">New Password</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('password') border-red-400 @enderror">
                        @error('password')
                            <p class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="px-8 py-3 bg-slate-900 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-700 transition shadow-sm">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    @include('dashboards.voter-dashboard.layout.footer')

    <script>
        /* Sync dark mode icon state on load */
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = localStorage.getItem('voterDarkMode') === 'true';
            document.querySelectorAll('.icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
            document.querySelectorAll('.icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
        });

        function toggleDark() {
            const isDark = document.documentElement.classList.toggle('dark-mode');
            localStorage.setItem('voterDarkMode', isDark);
            document.querySelectorAll('.icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
            document.querySelectorAll('.icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
        }
    </script>
</body>
</html>