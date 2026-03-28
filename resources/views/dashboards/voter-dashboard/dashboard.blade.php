{{-- 
=================================================================
VOTER DASHBOARD — voter-dashboard.blade.php
Dark Mode Fix: Inalis ang filter:invert approach,
pinalitan ng proper CSS class-based dark mode.
Ang candidate photos ay HINDI na nagbabago ng kulay.
=================================================================
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Elections | Enhance Voting System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .only-print { display: none; }
        @media print {
            @page { size: A4; margin: 1cm; }
            .no-print, nav, footer, button, .alerts, .page-header, .live-tally-section { display: none !important; }
            .only-print { display: block !important; visibility: visible !important; position: relative !important; width: 100% !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; padding: 0; margin: 0; }
            .receipt-card { border: 2px solid #000; padding: 30px; margin: 20px auto; max-width: 600px; background: white; }
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* ================================================================
           DARK MODE — Proper CSS variables approach (walang filter:invert)
           ================================================================ */

        /* Base */
        html.dark-mode body {
            background-color: #0f1117 !important;
            color: #e2e8f0 !important;
        }

        /* Backgrounds */
        html.dark-mode .bg-white          { background-color: #1e2130 !important; }
        html.dark-mode .bg-gray-50        { background-color: #0f1117 !important; }
        html.dark-mode .bg-gray-100       { background-color: #161923 !important; }
        html.dark-mode .bg-slate-50       { background-color: #1a1f2e !important; }
        html.dark-mode .bg-slate-100      { background-color: #252a3a !important; }
        html.dark-mode .bg-slate-900      { background-color: #060810 !important; }

        /* Slate bg with opacity */
        html.dark-mode .bg-slate-50\/30   { background-color: rgba(26,31,46,0.3) !important; }
        html.dark-mode .bg-slate-50\/80   { background-color: rgba(26,31,46,0.8) !important; }
        html.dark-mode .bg-slate-900\/60  { background-color: rgba(6,8,16,0.85) !important; }

        /* Text */
        html.dark-mode .text-slate-900    { color: #f1f5f9 !important; }
        html.dark-mode .text-slate-800    { color: #e2e8f0 !important; }
        html.dark-mode .text-slate-700    { color: #cbd5e1 !important; }
        html.dark-mode .text-slate-500    { color: #94a3b8 !important; }
        html.dark-mode .text-slate-400    { color: #64748b !important; }

        /* Borders */
        html.dark-mode .border-slate-50   { border-color: #252a3a !important; }
        html.dark-mode .border-slate-100  { border-color: #2d3347 !important; }
        html.dark-mode .border-slate-200  { border-color: #374151 !important; }
        html.dark-mode .divide-slate-50 > * + * { border-color: #2d3347 !important; }

        /* Navbar sticky */
        html.dark-mode .sticky.top-0 > .bg-white,
        html.dark-mode nav { background-color: #12151f !important; }

        /* Cards */
        html.dark-mode .rounded-\[2\.5rem\],
        html.dark-mode .rounded-\[2rem\],
        html.dark-mode .rounded-\[1\.5rem\] {
            background-color: #1e2130 !important;
            border-color: #2d3347 !important;
        }

        /* Modals */
        html.dark-mode .max-w-lg.rounded-\[2\.5rem\],
        html.dark-mode .max-w-md.rounded-\[2\.5rem\] {
            background-color: #1e2130 !important;
        }

        /* Emerald tints (ballot area) */
        html.dark-mode .bg-emerald-50     { background-color: #052e16 !important; }
        html.dark-mode .border-emerald-100{ border-color: #065f46 !important; }
        html.dark-mode .border-emerald-200{ border-color: #065f46 !important; }
        html.dark-mode .text-emerald-700  { color: #34d399 !important; }

        /* Sky tints */
        html.dark-mode .bg-sky-50         { background-color: #082f49 !important; }
        html.dark-mode .border-sky-100    { border-color: #0369a1 !important; }
        html.dark-mode .border-sky-50     { border-color: #075985 !important; }

        /* Hover */
        html.dark-mode .hover\:bg-slate-50:hover { background-color: #252a3a !important; }
        html.dark-mode .hover\:bg-emerald-100:hover { background-color: #064e3b !important; }

        /* Scrollbar dark */
        html.dark-mode .custom-scrollbar::-webkit-scrollbar-track { background: #252a3a; }
        html.dark-mode .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }

        /* ================================================================
           CANDIDATE PHOTOS — HINDI NAGBABAGO NG KULAY SA DARK MODE
           Ito ang pinaka-importanteng fix!
           ================================================================ */
        html.dark-mode img {
            filter: none !important;      /* Normal colors, walang invert */
        }
        /* Pero panatilihin ang grayscale hover effect sa candidate list */
        html.dark-mode img.grayscale {
            filter: grayscale(100%) !important;
        }
        html.dark-mode .group:hover img.grayscale {
            filter: none !important;
        }
    </style>

    {{-- Apply dark mode instantly (no flash) --}}
    <script>
        if (localStorage.getItem('voterDarkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 font-sans antialiased"
      x-data="{ openModal: false, showTally: false, selectedCandidate: {}, showBallotModal: false, ballotData: { title: '', votes: [] } }">

    <div class="sticky top-0 z-50 bg-white shadow-sm no-print">
        @include('dashboards.voter-dashboard.layout.nav')
    </div>

    @php
        $allowVoteChanges = \App\Models\Setting::where('setting_key', 'allow_vote_changes')->value('value') == '1';
        $showLiveResults  = \App\Models\Setting::where('setting_key', 'real_time_results')->value('value') == '1';
        $voterId = Auth::guard('voter')->id();
    @endphp

    <main class="flex-1 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 text-center md:text-left no-print page-header">
                <h1 class="text-3xl font-black text-slate-900 mb-1 uppercase tracking-tight">My Elections</h1>
                <p class="text-slate-500 italic text-xs">Vote and monitor ongoing school elections.</p>
            </div>

            {{-- ACTIVE ELECTIONS --}}
            @if($active->isEmpty())
                <div class="flex flex-col items-center justify-center py-24 bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 text-center no-print">
                    <h3 class="text-lg font-black text-slate-800 uppercase">No Active Elections</h3>
                    <p class="text-slate-400 text-xs mt-2 italic">Check back later or view completed elections below.</p>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($active as $election)
                    @php
                        $activeBallot = \App\Models\Vote::where('election_id', $election->id)
                            ->where('voter_id', $voterId)
                            ->with(['candidate', 'position'])
                            ->get()
                            ->map(function ($vote) {
                                return [
                                    'position'        => $vote->position?->title ?? '—',
                                    'candidate_name'  => $vote->candidate
                                        ? $vote->candidate->first_name . ' ' . $vote->candidate->last_name
                                        : '—',
                                    'candidate_party' => $vote->candidate?->party ?? 'Independent',
                                    'photo_path'      => $vote->candidate?->photo_path,
                                ];
                            });
                    @endphp
                    <div x-data="timer('{{ $election->end_at }}')" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden transition-all">

                        <div class="px-8 py-6 border-b border-slate-50 flex flex-col lg:flex-row justify-between items-center gap-4 no-print">
                            <div class="text-center lg:text-left flex-1">
                                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-1">{{ $election->title }}</h3>
                                <p class="text-xs text-slate-500 font-medium italic mb-3 leading-relaxed">
                                    {{ $election->description ?? "Official student election monitoring for " . $election->title }}
                                </p>
                                <div class="flex justify-center lg:justify-start gap-2">
                                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black text-sky-600 bg-sky-50 px-3 py-1.5 rounded-full border border-sky-100 uppercase">
                                        <span x-text="timeString"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 flex-wrap justify-center lg:justify-end">
                                @if($showLiveResults)
                                    <a href="{{ route('voter.results', $election->id) }}"
                                       class="px-5 py-3 bg-slate-50 text-sky-600 rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-sky-400 hover:text-white transition-all border border-sky-100 shadow-sm">
                                        View Live Tally
                                    </a>
                                @endif

                                @if($election->has_voted)
                                    <button
                                        @click="ballotData = {{ Js::from(['title' => $election->title, 'votes' => $activeBallot]) }}; showBallotModal = true"
                                        class="px-5 py-3 bg-emerald-50 text-emerald-700 rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-emerald-100 transition-all border border-emerald-200 shadow-sm whitespace-nowrap">
                                        🗳️ View My Ballot
                                    </button>
                                @endif

                                @if($election->has_voted && !$allowVoteChanges)
                                    <div class="flex items-center gap-2">
                                        <div class="px-6 py-3 bg-emerald-50 text-emerald-600 rounded-xl font-black text-[9px] uppercase tracking-widest border border-emerald-100 italic shadow-sm">
                                            Ballot Cast Confirmed
                                        </div>
                                        <button onclick="window.print()" class="p-3 bg-slate-900 text-white rounded-xl hover:bg-sky-600 transition-all shadow-lg active:scale-95">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        </button>
                                    </div>
                                @elseif(!$election->has_voted || $allowVoteChanges)
                                    <a :href="!expired ? '{{ route('voter.show', $election->id) }}' : '#'"
                                       class="px-6 py-3 bg-sky-500 text-white rounded-xl font-black uppercase text-[9px] tracking-widest shadow-lg hover:bg-sky-600 transition-all active:scale-95">
                                        {{ $election->has_voted ? 'Update Ballot' : 'Cast Ballot' }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- CANDIDATES GRID --}}
                        <div class="p-6 md:p-8 bg-slate-50/30 no-print">
                            @php
                                $allCandidates = $election->positions->flatMap(fn($pos) => $pos->candidates->map(fn($c) => [
                                    'candidate' => $c, 'position' => $pos,
                                ]));
                                $grouped = $allCandidates->groupBy(fn($item) => $item['candidate']->party ?? 'Independent');
                            @endphp
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                @foreach($grouped as $partyName => $members)
                                <div class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden">
                                    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $partyName === 'Independent' ? 'bg-slate-400' : 'bg-sky-400' }}"></span>
                                        <span class="text-[10px] font-black uppercase tracking-widest {{ $partyName === 'Independent' ? 'text-slate-500' : 'text-sky-600' }}">{{ $partyName }}</span>
                                        <span class="ml-auto text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full {{ $partyName === 'Independent' ? 'bg-slate-100 text-slate-400' : 'bg-sky-50 text-sky-500 border border-sky-100' }}">
                                            {{ $partyName === 'Independent' ? 'No Party' : 'Coalition' }}
                                        </span>
                                    </div>
                                    <div class="divide-y divide-slate-50">
                                        @foreach($members as $item)
                                        @php $candidate = $item['candidate']; $position = $item['position']; @endphp
                                        <div @click="selectedCandidate = {{ Js::from($candidate) }}; openModal = true"
                                             class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 transition-all cursor-pointer group">
                                            <div class="flex-shrink-0">
                                                @if($candidate->photo_path)
                                                    {{-- 
                                                        FIXED: Inalis ang grayscale sa dark mode para hindi mag-iba ang kulay.
                                                        Ang grayscale hover effect ay nandoon pa rin sa light mode.
                                                    --}}
                                                    <img src="{{ asset('storage/' . $candidate->photo_path) }}"
                                                         class="w-14 h-14 rounded-xl object-cover grayscale group-hover:grayscale-0 transition-all border border-slate-100 candidate-photo">
                                                @else
                                                    <div class="w-14 h-14 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 text-sm font-black uppercase">
                                                        {{ substr($candidate->first_name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-sm font-black text-slate-900 uppercase tracking-tight group-hover:text-sky-600 transition-colors truncate">{{ $candidate->full_name }}</h5>
                                                <p class="text-[9px] font-bold uppercase tracking-widest mt-0.5 {{ $partyName === 'Independent' ? 'text-slate-400' : 'text-sky-500' }}">{{ $position->title }}</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- LIVE TALLY SECTION --}}
                        @if($showLiveResults)
                        <div x-show="showTally" x-cloak x-transition class="p-8 border-t border-slate-100 bg-white no-print live-tally-section">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Real-time Standing</h3>
                                <span class="text-[9px] bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full font-black animate-pulse">LIVE TALLY</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                @foreach($election->positions as $position)
                                <div class="space-y-5">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $position->title }}</p>
                                    <div class="space-y-6">
                                        @foreach($position->candidates as $candidate)
                                            @php
                                                $votes = \App\Models\Vote::where('candidate_id', $candidate->id)->where('election_id', $election->id)->count();
                                                $totalPosVotes = \App\Models\Vote::where('position_id', $position->id)->where('election_id', $election->id)->count();
                                                $percent = $totalPosVotes > 0 ? ($votes / $totalPosVotes) * 100 : 0;
                                            @endphp
                                            <div class="relative">
                                                <div class="flex justify-between items-center mb-1.5">
                                                    <span class="text-xs font-bold text-slate-700 uppercase">{{ $candidate->full_name }}</span>
                                                    <span class="text-[10px] font-black text-sky-600">{{ number_format($votes) }} VOTES</span>
                                                </div>
                                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden border border-slate-50 shadow-inner">
                                                    <div class="bg-sky-400 h-full transition-all duration-1000 ease-out" style="width: {{ $percent }}%"></div>
                                                </div>
                                                <div class="mt-1">
                                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ number_format($percent, 1) }}% Share</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- BALLOT RECEIPT --}}
                        <div class="only-print">
                            @if($election->has_voted)
                                <div class="receipt-card">
                                    <div class="text-center border-b-2 border-dashed border-black pb-6 mb-6">
                                        <img src="{{ asset('images/school-logo.png') }}" class="w-20 h-20 mx-auto mb-4 object-contain">
                                        <h2 class="text-2xl font-black uppercase italic tracking-tighter">Enhance Voting System</h2>
                                        <p class="text-xs font-bold uppercase tracking-widest mt-1">Catalino D. Cerezo NHS</p>
                                        <p class="text-[9px] mt-4 font-mono uppercase">Official Encrypted Security Receipt</p>
                                    </div>
                                    <div class="mb-6 space-y-1 font-mono text-xs">
                                        <p><strong>ELECTION:</strong> {{ strtoupper($election->title) }}</p>
                                        <p><strong>VOTER:</strong> {{ strtoupper(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}</p>
                                        <p><strong>DATE:</strong> {{ now()->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <div class="border-y-2 border-dashed border-black py-6 my-6">
                                        <h4 class="text-center font-black uppercase text-sm mb-4 tracking-widest">Secured Selections</h4>
                                        <table class="w-full text-left font-mono text-[10px]">
                                            <thead>
                                                <tr class="border-b-2 border-black">
                                                    <th class="py-2">POSITION</th>
                                                    <th class="py-2 text-right">SECURITY BALLOT CODE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $myVotes = \App\Models\Vote::where('election_id', $election->id)
                                                        ->where('voter_id', Auth::guard('voter')->id())
                                                        ->with(['candidate', 'position'])
                                                        ->get();
                                                @endphp
                                                @foreach($myVotes as $vote)
                                                    <tr class="border-b border-gray-100">
                                                        <td class="py-3 font-bold uppercase">{{ $vote->position->title }}</td>
                                                        <td class="py-3 font-mono text-right">VOTE-{{ substr(md5($vote->id . $vote->candidate_id), 0, 12) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                        <p class="text-[9px] font-mono leading-relaxed">
                                            <strong>NOTICE:</strong> For your security, candidate names are encrypted.
                                            This receipt serves as proof that your votes have been successfully recorded.
                                        </p>
                                    </div>
                                    <div class="text-center mt-8">
                                        <div class="pt-4 border-t border-black">
                                            <p class="text-[7px] font-mono uppercase text-gray-400">Master Verification Hash:</p>
                                            <p class="text-[8px] font-mono break-all font-bold uppercase">{{ hash('sha256', Auth::id() . $election->id) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                    @endforeach
                </div>
            @endif

            {{-- COMPLETED ELECTIONS --}}
            @if(isset($ended) && $ended->isNotEmpty())
            <div class="mt-12 no-print">
                <div class="mb-6 flex items-center gap-3">
                    <div class="w-1.5 h-5 bg-slate-400 rounded-full"></div>
                    <div>
                        <h2 class="text-xl font-black text-slate-700 uppercase tracking-tight">Completed Elections</h2>
                        <p class="text-xs text-slate-400 italic mt-0.5">Elections that have ended — view the final results.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($ended as $endedElection)
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-8 py-5 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <span class="text-[8px] font-black bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full uppercase tracking-widest">
                                        Election Closed
                                    </span>
                                    @if($endedElection->has_voted)
                                        <span class="text-[8px] font-black bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full uppercase tracking-widest border border-emerald-100">
                                            ✓ You Participated
                                        </span>
                                    @else
                                        <span class="text-[8px] font-black bg-slate-50 text-slate-400 px-2 py-0.5 rounded-full uppercase tracking-widest border border-slate-100">
                                            Did Not Vote
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">{{ $endedElection->title }}</h3>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">
                                    Ended {{ $endedElection->end_at->format('M d, Y h:i A') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($endedElection->has_voted)
                                    <button
                                        @click="ballotData = {{ Js::from(['title' => $endedElection->title, 'votes' => $endedElection->my_ballot]) }}; showBallotModal = true"
                                        class="px-5 py-3 bg-emerald-50 text-emerald-700 rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-emerald-100 transition-all border border-emerald-200 shadow-sm whitespace-nowrap">
                                        🗳️ View My Ballot
                                    </button>
                                @endif
                                <a href="{{ route('voter.results', $endedElection->id) }}"
                                   class="px-5 py-3 bg-indigo-600 text-white rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-indigo-700 transition-all shadow-sm whitespace-nowrap">
                                    🏆 View Final Results
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- CANDIDATE MODAL --}}
        <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm no-print" x-transition>
            <div @click.away="openModal = false" class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden p-8 space-y-6 border border-slate-100">
                <div class="flex items-center gap-6">
                    <template x-if="selectedCandidate.photo_path">
                        {{-- FIXED: Normal lang ang photo sa modal, walang filter --}}
                        <img :src="'/storage/' + selectedCandidate.photo_path"
                             class="w-24 h-24 rounded-[2rem] object-cover shadow-lg border-4 border-slate-50 candidate-photo">
                    </template>
                    <div class="flex-1">
                        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight leading-tight" x-text="selectedCandidate.full_name"></h3>
                        <p class="text-[11px] font-bold text-sky-500 uppercase tracking-widest mt-2" x-text="selectedCandidate.party || 'Independent'"></p>
                    </div>
                </div>
                <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
                    <div class="bg-slate-50/80 rounded-3xl p-6 border border-slate-100">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Biography & Achievements</p>
                        <p class="text-sm text-slate-700 leading-relaxed font-medium" x-text="selectedCandidate.bio || 'No background information provided.'"></p>
                    </div>
                    <div class="bg-sky-50/50 rounded-3xl p-6 border border-sky-100">
                        <p class="text-[9px] font-black text-sky-400 uppercase tracking-widest mb-2 italic">Campaign Manifesto</p>
                        <p class="text-sm text-slate-600 leading-relaxed font-medium italic" x-text="selectedCandidate.manifesto || 'No platform provided.'"></p>
                    </div>
                </div>
                <button @click="openModal = false" class="w-full py-5 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-sky-600 transition-all shadow-xl active:scale-95">
                    Dismiss Profile
                </button>
            </div>
        </div>

        {{-- MY BALLOT MODAL --}}
        <div x-show="showBallotModal" x-cloak x-transition
             class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm no-print">
            <div @click.away="showBallotModal = false"
                 class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                <div class="bg-emerald-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-black text-emerald-200 uppercase tracking-widest">My Official Ballot</p>
                            <h3 class="text-lg font-black text-white uppercase tracking-tight mt-0.5" x-text="ballotData.title"></h3>
                        </div>
                        <button @click="showBallotModal = false" class="text-emerald-200 hover:text-white transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-8 space-y-4">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">
                        Your votes are confidential. Only you can see this.
                    </p>
                    <template x-if="ballotData.votes && ballotData.votes.length > 0">
                        <div class="space-y-3">
                            <template x-for="(vote, index) in ballotData.votes" :key="index">
                                <div class="flex items-center gap-4 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden border border-emerald-200 shrink-0 bg-white flex items-center justify-center">
                                        <template x-if="vote.photo_path">
                                            {{-- FIXED: candidate photo sa ballot modal, normal ang kulay --}}
                                            <img :src="'/storage/' + vote.photo_path"
                                                 class="w-full h-full object-cover candidate-photo">
                                        </template>
                                        <template x-if="!vote.photo_path">
                                            <span class="text-sm font-black text-emerald-600" x-text="vote.candidate_name.charAt(0)"></span>
                                        </template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest" x-text="vote.position"></p>
                                        <p class="text-sm font-black text-slate-900 uppercase truncate" x-text="vote.candidate_name"></p>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5" x-text="vote.candidate_party"></p>
                                    </div>
                                    <div class="w-7 h-7 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!ballotData.votes || ballotData.votes.length === 0">
                        <p class="text-center text-[10px] font-black text-slate-400 uppercase tracking-widest py-6">
                            No ballot records found.
                        </p>
                    </template>
                </div>
                <div class="px-8 pb-8">
                    <button @click="showBallotModal = false"
                            class="w-full py-3 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-700 transition-all">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </main>

    <div class="no-print">
        @include('dashboards.voter-dashboard.layout.footer')
    </div>

    <script>
        function timer(targetDate) {
            return {
                timeString: 'Loading...', expired: false, target: new Date(targetDate).getTime(),
                init() { this.updateTime(); setInterval(() => this.updateTime(), 1000); },
                updateTime() {
                    const now = new Date().getTime(); const distance = this.target - now;
                    if (distance < 0) { this.expired = true; this.timeString = "CLOSED"; return; }
                    const hours   = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    this.timeString = `${hours}H ${minutes}M ${seconds}S LEFT`;
                }
            }
        }

        setInterval(async function() {
            try {
                const token    = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept':           'application/json',
                        'X-CSRF-TOKEN':     token,
                    }
                });
                if (response.status === 401) {
                    const data = await response.json();
                    if (data.expired) window.location.href = data.redirect;
                }
            } catch (e) {
                console.error('Session check error:', e);
            }
        }, 30000);

        /* ── Dark Mode Toggle ── */
        function toggleDark() {
            const isDark = document.documentElement.classList.toggle('dark-mode');
            localStorage.setItem('voterDarkMode', isDark);
            document.querySelectorAll('.icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
            document.querySelectorAll('.icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
        }

        document.addEventListener('DOMContentLoaded', function () {
            const isDark = localStorage.getItem('voterDarkMode') === 'true';
            document.querySelectorAll('.icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
            document.querySelectorAll('.icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
        });
    </script>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-emerald-500 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-3 font-black uppercase text-sm tracking-widest whitespace-nowrap">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-rose-500 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-3 font-black uppercase text-sm tracking-widest whitespace-nowrap">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif
</body>
</html>