<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ballot Confirmed | Enhance Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 font-sans antialiased">

    @include('dashboards.voter-dashboard.layout.nav')

    <main class="flex-1 flex items-center justify-center py-16 px-4">
        <div class="w-full max-w-lg text-center">

            {{-- Icon --}}
            <div class="w-24 h-24 bg-emerald-50 border-2 border-emerald-100 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-sm">
                <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl font-black uppercase text-slate-900 tracking-tighter mb-2">Ballot Already Cast</h1>
            <p class="text-slate-500 text-sm font-medium mb-8">
                Your official ballot for <span class="font-black text-slate-700 uppercase">{{ $election->title }}</span> has already been recorded in our system.
            </p>

            {{-- Info Card --}}
            <div class="bg-white border border-slate-100 rounded-[2rem] shadow-sm p-8 mb-8 text-left space-y-4">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-50">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ballot Summary</p>
                </div>

                @php
                    $myVotes = \App\Models\Vote::where('election_id', $election->id)
                        ->where('voter_id', auth('voter')->id())
                        ->with(['position', 'candidate'])
                        ->get();
                @endphp

                @foreach($myVotes as $vote)
                    <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $vote->position->title }}</p>
                            <p class="text-sm font-black text-slate-800 uppercase mt-0.5">
                                {{ $vote->candidate->first_name }} {{ $vote->candidate->last_name }}
                            </p>
                        </div>
                        <span class="text-[8px] font-black uppercase tracking-widest px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full">
                            Recorded ✓
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Notice --}}
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mb-8">
                Your vote is final and cannot be changed.
            </p>

            {{-- Back Button --}}
            <a href="{{ route('voter.dashboard') }}"
               class="inline-flex items-center gap-3 px-10 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-600 shadow-xl transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>

        </div>
    </main>

    @include('dashboards.voter-dashboard.layout.footer')

</body>
</html>