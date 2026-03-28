<x-app-layout>
    {{-- CONFETTI PARA SA WINNERS --}}
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <style>
        /* Screen Layout */
        @media screen { 
            .only-print { display: none; } 
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #bae6fd; border-radius: 10px; }
        }
        /* Print Layout */
        @media print {
            @page { size: A4; margin: 1.5cm; }

            .no-print,
            nav,
            header,
            aside,
            footer,
            .sidebar,
            button,
            [role="navigation"],
            [x-data],
            .fi-sidebar,
            .fi-topbar,
            .fi-header,
            .fi-nav,
            .navigation,
            .navbar { display: none !important; }

            body {
                background: white !important;
                color: black !important;
                font-family: 'Arial', sans-serif !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            #app,
            .app-layout,
            main,
            [class*="max-w"],
            [class*="mx-auto"] {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .only-print {
                display: block !important;
                visibility: visible !important;
                position: relative !important;
                width: 100% !important;
            }

            table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 2.5px solid black !important;
                table-layout: fixed;
                margin-top: 30px;
            }

            td, th {
                border: 1.5px solid black !important;
                padding: 12px !important;
                word-wrap: break-word;
            }

            .signature-container {
                margin-top: 80px;
                display: flex !important;
                justify-content: space-between !important;
                padding: 0 50px;
            }

            .sig-box { width: 250px; text-align: center; }
            .sig-line { border-top: 2px solid black; margin-bottom: 8px; width: 100%; }
        }
    </style>

    @php
        $isElectionEnded = $election->end_at <= now();
        // Label at kulay ng modal depende sa status ng election
        $modalTitle      = $isElectionEnded ? '🏆 Official Winners' : '📊 Current Standings';
        $modalBgClass    = $isElectionEnded ? 'bg-indigo-600' : 'bg-sky-500';
        $buttonLabel     = $isElectionEnded ? '🏆 View Winners' : '📊 View Standings';
        $buttonBgClass   = $isElectionEnded ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-sky-500 hover:bg-sky-600';
    @endphp

    <div class="max-w-7xl mx-auto py-8 px-4 space-y-10">
        @if($election)
            {{-- 1. DASHBOARD VIEW (NO-PRINT) --}}
            <div class="no-print space-y-8" x-data="{ showWinners: false }">

                {{-- Header Section --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-black uppercase text-sky-400 mb-1">Results Monitoring</p>
                            <h2 class="text-3xl font-black uppercase text-slate-900 leading-none">{{ $election->title }}</h2>
                            <p class="text-xs text-slate-500 italic mt-2">{{ $election->description ?? $election->title }}</p>
                            {{-- Status badge --}}
                            @if($isElectionEnded)
                                <span class="inline-flex items-center mt-3 rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black text-emerald-600 border border-emerald-100 uppercase tracking-widest">
                                    ✅ Election Ended
                                </span>
                            @else
                                <span class="inline-flex items-center mt-3 rounded-full bg-sky-50 px-3 py-1 text-[10px] font-black text-sky-600 border border-sky-100 uppercase tracking-widest animate-pulse">
                                    ● Election Ongoing
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-4">
                            {{-- Total Turnout --}}
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase text-slate-400">Total Turnout</p>
                                <p class="text-4xl font-black text-indigo-600 tracking-tighter">{{ $totalUniqueVoters }}</p>
                            </div>
                            {{-- BUTTONS --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.results.index') }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-black uppercase text-[9px] tracking-widest transition-all">
                                    ← Back
                                </a>
                                <button @click="showWinners = true; $nextTick(() => { @if($isElectionEnded) if(typeof confetti !== 'undefined') confetti({ particleCount: 120, spread: 80, origin: { y: 0.5 } }) @endif })"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white rounded-xl font-black uppercase text-[9px] tracking-widest transition-all shadow-lg {{ $buttonBgClass }}">
                                    {{ $buttonLabel }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- POSITION-BASED GRID --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach($election->positions as $position)
                        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-sm overflow-hidden flex flex-col">
                            <div class="bg-indigo-50 px-8 py-5 border-b border-indigo-100">
                                <h3 class="font-black text-indigo-900 uppercase text-sm tracking-widest">🏷️ {{ $position->title }}</h3>
                            </div>
                            <div class="p-8 space-y-6 flex-1">
                                @foreach($position->candidates as $candidate)
                                    @php
                                        $votes = \App\Models\Vote::where('candidate_id', $candidate->id)->count();
                                        $percent = $totalUniqueVoters > 0 ? ($votes / $totalUniqueVoters) * 100 : 0;
                                    @endphp
                                    <div class="relative">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-black text-indigo-500 uppercase tracking-widest">
                                                    {{ $candidate->party ?? 'Independent' }}
                                                </span>
                                                <span class="text-xs font-black text-slate-700 uppercase">
                                                    {{ $candidate->first_name }} {{ $candidate->last_name }}
                                                </span>
                                            </div>
                                            <span class="text-xl font-black text-slate-900">{{ number_format($votes) }}</span>
                                        </div>
                                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden shadow-inner">
                                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- WINNERS / STANDINGS MODAL --}}
                <div x-show="showWinners" x-cloak 
                     class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm"
                     x-transition>
                    <div @click.away="showWinners = false" 
                         class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
                        
                        {{-- Modal Header — nagbabago depende sa status --}}
                        <div class="px-8 py-6 {{ $modalBgClass }}">
                            <h3 class="text-xl font-black text-white uppercase tracking-widest">{{ $modalTitle }}</h3>
                            <p class="text-white/70 text-[10px] uppercase tracking-widest mt-1">{{ $election->title }}</p>
                        </div>

                        <div class="p-8 space-y-4 max-h-[55vh] overflow-y-auto custom-scrollbar">
                            @foreach($election->positions as $position)
                                @php
                                    $topVotes = $position->candidates->map(function($c) {
                                        return [
                                            'candidate' => $c,
                                            'votes' => \App\Models\Vote::where('candidate_id', $c->id)->count(),
                                        ];
                                    })->sortByDesc('votes');
                                    $maxVotes = $topVotes->first()['votes'] ?? 0;
                                    $winners  = $topVotes->filter(fn($c) => $c['votes'] === $maxVotes && $maxVotes > 0);
                                    $isTie    = $winners->count() > 1;
                                @endphp
                                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                    <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-3">{{ $position->title }}</p>
                                    @if($winners->isEmpty())
                                        <p class="text-xs text-slate-400 italic">No votes recorded</p>
                                    @else
                                        @foreach($winners as $w)
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-black text-slate-900 uppercase text-sm">
                                                    {{ $w['candidate']->first_name }} {{ $w['candidate']->last_name }}
                                                    @if($isTie)
                                                        <span class="text-amber-500 text-[9px] ml-1">TIE</span>
                                                    @endif
                                                </p>
                                                <p class="text-[9px] text-slate-400 uppercase tracking-widest">{{ $w['candidate']->party ?? 'Independent' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-2xl font-black text-indigo-600">{{ number_format($w['votes']) }}</p>
                                                <p class="text-[8px] text-slate-400 uppercase">votes</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-8 pb-8 flex gap-3">
                            @if($isElectionEnded)
                                <button onclick="window.print()"
                                        class="flex-1 py-4 bg-slate-800 hover:bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">
                                    🖨️ Print Results
                                </button>
                            @else
                                <button disabled
                                        class="flex-1 py-4 bg-slate-200 text-slate-400 rounded-2xl font-black uppercase text-[10px] tracking-widest cursor-not-allowed"
                                        title="Printing is disabled while the election is ongoing.">
                                    🔒 Election Ongoing
                                </button>
                            @endif
                            <button @click="showWinners = false" 
                                    class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all">
                                Close
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- 2. PRINT VIEW: OFFICIAL CERTIFICATE --}}
            <div class="only-print">
                <div class="flex items-center gap-6 mb-8 border-b-4 border-black pb-6">
                    <img src="{{ asset('images/school-logo.png') }}" class="w-24 h-24">
                    <div>
                        <h1 class="text-xl font-bold uppercase tracking-tight">Catalino D. Cerezo National High School</h1>
                        <h2 class="text-3xl font-black uppercase tracking-widest text-slate-900 leading-none">Official Certificate of Canvass</h2>
                    </div>
                </div>

                <div class="border-2 border-black p-5 text-center font-black uppercase mb-8">
                    {{ $election->title }} Final Results Certification
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 25%;">Official Position</th>
                            <th style="text-align: left; width: 55%;">Certified Winner / Tie Status</th>
                            <th style="text-align: center; width: 20%;">Final Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($election->positions as $pos)
                            @php 
                                $maxV    = $pos->candidates->max('votes_count'); 
                                $winList = $pos->candidates->where('votes_count', $maxV)->where('votes_count', '>', 0); 
                            @endphp
                            <tr>
                                <td class="uppercase font-bold">{{ $pos->title }}</td>
                                <td>
                                    @if($winList->count() > 1) 
                                        @foreach($winList as $w) 
                                            <div class="font-bold uppercase underline">• {{ $w->first_name }} {{ $w->last_name }} (TIE)</div> 
                                        @endforeach
                                    @elseif($winList->count() == 1)
                                        <div class="font-bold uppercase underline underline-offset-4">{{ $winList->first()->first_name }} {{ $winList->first()->last_name }}</div>
                                    @else
                                        <span class="text-slate-400 italic">No Record</span>
                                    @endif
                                </td>
                                <td style="text-align: center;" class="font-bold">{{ number_format($maxV) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="signature-container">
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <span class="font-black text-[12px] uppercase">Election Chairman</span>
                    </div>
                    <div class="sig-box">
                        <div class="sig-line"></div>
                        <span class="font-black text-[12px] uppercase">School Principal</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>