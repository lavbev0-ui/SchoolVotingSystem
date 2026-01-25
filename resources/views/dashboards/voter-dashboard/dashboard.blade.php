<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Elections | Enhance Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 font-sans antialiased">

    {{-- NAVIGATION --}}
    @include('dashboards.voter-dashboard.layout.nav')

    {{-- MAIN CONTENT --}}
    <main class="flex-1 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. SETUP & LOGIC --}}
            @php
                // Filter Collections
                $active    = $elections->where('status', 'active');
                $upcoming  = $elections->where('status', 'upcoming');
                $completed = $elections->where('status', 'completed');

                // Check Real-time setting (Fetch once)
                $showLiveResults = \App\Models\Setting::where('key', 'real_time_results')->value('value') === '1';
            @endphp

            {{-- 2. PAGE HEADER --}}
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Elections</h1>
                <p class="text-gray-500">
                    Vote in elections you're eligible for and view your voting history.
                </p>
            </div>

            {{-- 3. GLOBAL EMPTY STATE --}}
            @if($elections->isEmpty())
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-gray-300 text-center">
                    <div class="p-4 rounded-full bg-gray-50 mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">No Elections Available</h3>
                    <p class="text-gray-500 mt-1">There are currently no elections you are eligible to vote in.</p>
                </div>
            @else

                {{-- SECTION: ACTIVE ELECTIONS --}}
                @if($active->isNotEmpty())
                    <section class="mb-14">
                        <div class="flex items-center gap-3 mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800">Active Elections</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                {{ $active->count() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($active as $election)
                                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full overflow-hidden">
                                    <div class="p-6 flex-1">
                                        <div class="flex justify-between items-start mb-4">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                                <span class="relative flex h-2 w-2">
                                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                </span>
                                                Active
                                            </span>
                                            @if($showLiveResults)
                                                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100">
                                                    Live Results
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-1" title="{{ $election->title }}">
                                            {{ $election->title }}
                                        </h3>
                                        <p class="text-sm text-gray-500 line-clamp-2 mb-4 h-10">
                                            {{ $election->description ?? 'No description provided.' }}
                                        </p>

                                        <div class="space-y-1 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <span>📅</span>
                                                <span>Ends: {{ \Carbon\Carbon::parse($election->end_at)->format('M d, Y h:i A') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span>👥</span>
                                                <span>{{ $election->candidates_count ?? 'Multiple' }} Candidates</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-gray-50 border-t border-gray-100 space-y-3">
                                        <div class="p-4 bg-gray-50 border-t border-gray-100 space-y-3">
                                            {{-- CHECK: Has the user voted in this election? --}}
                                            @if(in_array($election->id, $votedElectionIds))
                                            
                                                {{-- CASE 1: ALREADY VOTED --}}
                                                <button disabled class="flex items-center justify-center w-full rounded-xl bg-green-50 text-green-600 font-semibold py-2.5 cursor-not-allowed border border-green-200">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Voted
                                                </button>

                                            @else

                                                {{-- CASE 2: NOT VOTED YET --}}
                                                <a href="{{ route('voter.election.show', $election->id) }}" class="flex items-center justify-center w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 transition-colors">
                                                    Cast Your Vote
                                                </a>

                                            @endif

                                            {{-- Live Results Button (Always show if enabled) --}}
                                            @if($showLiveResults)
                                                <a href="{{ route('voter.election.result', $election->id) }}" class="flex items-center justify-center w-full rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-semibold py-2.5 transition-colors">
                                                    View Live Results
                                                </a>
                                            @endif
                                        </div>
                                        @if($showLiveResults)
                                            <a href="{{ route('voter.election.result', $election->id) }}" class="flex items-center justify-center w-full rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-semibold py-2.5 transition-colors">
                                                View Live Results
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- SECTION: UPCOMING ELECTIONS --}}
                @if($upcoming->isNotEmpty())
                    <section class="mb-14">
                        <div class="flex items-center gap-3 mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800">Upcoming</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $upcoming->count() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($upcoming as $election)
                                <div class="bg-white/80 rounded-2xl border border-gray-200 flex flex-col h-full">
                                    <div class="p-6 flex-1 opacity-75">
                                        <span class="inline-block mb-3 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Starts Soon
                                        </span>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-1">{{ $election->title }}</h3>
                                        <p class="text-sm text-gray-500 line-clamp-2 mb-4">{{ $election->description }}</p>
                                        <div class="text-sm text-gray-600 font-medium bg-gray-50 p-2 rounded-lg inline-block">
                                            Opens: {{ \Carbon\Carbon::parse($election->start_at)->format('M d, Y @ h:i A') }}
                                        </div>
                                    </div>
                                    <div class="p-4 border-t border-gray-100">
                                        <button disabled class="w-full rounded-xl bg-gray-100 text-gray-400 font-semibold py-2.5 cursor-not-allowed border border-transparent">
                                            Not Yet Open
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- SECTION: COMPLETED ELECTIONS --}}
                @if($completed->isNotEmpty())
                    <section>
                        <div class="flex items-center gap-3 mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800">Completed</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $completed->count() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($completed as $election)
                                <div class="bg-gray-50 rounded-2xl border border-gray-200 flex flex-col h-full">
                                    <div class="p-6 flex-1">
                                        <div class="flex justify-between mb-3">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border border-gray-300 text-gray-500">
                                                Ended
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-700 mb-2 line-clamp-1">{{ $election->title }}</h3>
                                        <p class="text-sm text-gray-500 line-clamp-2">{{ $election->description }}</p>
                                    </div>
                                    <div class="p-4 border-t border-gray-200 bg-white rounded-b-2xl">
                                        <a href="{{ route('voter.election.result', $election->id) }}" class="flex items-center justify-center w-full rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-semibold py-2.5 transition-colors">
                                            View Results
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

            @endif
        </div>
    </main>

    {{-- FOOTER --}}
    @include('dashboards.voter-dashboard.layout.footer')

</body>
</html>