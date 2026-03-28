<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Election Results | Enhance Voting System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .only-print { display: none; }
        @media print {
            @page { size: A4; margin: 1.5cm; }
            .no-print, nav, footer, button { display: none !important; }
            .only-print { display: block !important; visibility: visible !important; width: 100% !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; padding: 0; margin: 0; }
            table { width: 100% !important; border-collapse: collapse !important; border: 2px solid black !important; }
            td, th { border: 1px solid black !important; padding: 10px !important; }
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bae6fd; border-radius: 10px; }

        /* ================================================================
           DARK MODE — Results Page
           ================================================================ */
        html.dark-mode body {
            background-color: #0f1117 !important;
            color: #e2e8f0 !important;
        }

        /* Backgrounds */
        html.dark-mode .bg-white          { background-color: #1e2130 !important; }
        html.dark-mode .bg-gray-50        { background-color: #0f1117 !important; }
        html.dark-mode .bg-slate-50       { background-color: #1a1f2e !important; }
        html.dark-mode .bg-slate-100      { background-color: #252a3a !important; }
        html.dark-mode .bg-indigo-50      { background-color: #1e1a3a !important; }
        html.dark-mode .bg-emerald-50     { background-color: #052e16 !important; }
        html.dark-mode .bg-sky-50         { background-color: #082f49 !important; }

        /* Text */
        html.dark-mode .text-slate-900    { color: #f1f5f9 !important; }
        html.dark-mode .text-slate-800    { color: #e2e8f0 !important; }
        html.dark-mode .text-slate-700    { color: #cbd5e1 !important; }
        html.dark-mode .text-slate-400    { color: #64748b !important; }
        html.dark-mode .text-indigo-900   { color: #c7d2fe !important; }
        html.dark-mode .text-indigo-600   { color: #818cf8 !important; }
        html.dark-mode .text-indigo-400   { color: #a5b4fc !important; }
        html.dark-mode .text-slate-700.rounded-xl { color: #e2e8f0 !important; }

        /* Borders */
        html.dark-mode .border-slate-100  { border-color: #2d3347 !important; }
        html.dark-mode .border-indigo-100 { border-color: #312e6e !important; }
        html.dark-mode .border-emerald-100{ border-color: #065f46 !important; }
        html.dark-mode .border-sky-100    { border-color: #0369a1 !important; }

        /* Cards */
        html.dark-mode .rounded-\[2\.5rem\] {
            background-color: #1e2130 !important;
            border-color: #2d3347 !important;
        }
        html.dark-mode .rounded-2xl {
            background-color: #1e2130 !important;
        }

        /* Position header inside card */
        html.dark-mode .bg-indigo-50.border-b.border-indigo-100 {
            background-color: #1e1a3a !important;
            border-color: #312e6e !important;
        }

        /* Candidate list items */
        html.dark-mode .bg-indigo-50.border.border-indigo-100 {
            background-color: #1e1a3a !important;
            border-color: #312e6e !important;
        }

        /* Navbar */
        html.dark-mode .sticky.top-0 > .bg-white { background-color: #12151f !important; }
        html.dark-mode nav { background-color: #12151f !important; }

        /* Winner banner */
        html.dark-mode .bg-emerald-50.border.border-emerald-100 {
            background-color: #052e16 !important;
            border-color: #065f46 !important;
        }

        /* Chart.js dark mode */
        html.dark-mode canvas {
            filter: none !important;
        }

        /* Back button */
        html.dark-mode .bg-slate-100.text-slate-700 {
            background-color: #252a3a !important;
            color: #e2e8f0 !important;
        }
        html.dark-mode .hover\:bg-slate-200:hover {
            background-color: #374151 !important;
        }

        /* Images */
        html.dark-mode img { filter: none !important; }

        /* Grid chart text */
        html.dark-mode .text-slate-400 { color: #64748b !important; }
    </style>

    {{-- Apply dark mode instantly, no flash --}}
    <script>
        if (localStorage.getItem('voterDarkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 font-sans antialiased">

    <div class="sticky top-0 z-50 bg-white shadow-sm no-print">
        @include('dashboards.voter-dashboard.layout.nav')
    </div>

    @php
        $isElectionEnded = \Carbon\Carbon::parse($results->end_at)->isPast();
        $voterId         = Auth::guard('voter')->id();
    @endphp

    <main class="flex-1 py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- HEADER --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm px-8 py-7 no-print">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <p class="text-[10px] font-black uppercase text-sky-400 mb-1 tracking-widest">
                            {{ $isElectionEnded ? 'Official Final Results' : 'Live Tally' }}
                        </p>
                        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight leading-none">
                            {{ $results->title }}
                        </h1>
                        <p class="text-xs text-slate-400 italic mt-2">
                            {{ $results->description ?? $results->title }}
                        </p>
                        <div class="mt-3">
                            @if($isElectionEnded)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-black text-emerald-600 border border-emerald-100 uppercase tracking-widest">
                                    ✅ Election Ended — {{ \Carbon\Carbon::parse($results->end_at)->format('M d, Y h:i A') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-[10px] font-black text-sky-600 border border-sky-100 uppercase tracking-widest animate-pulse">
                                    ● Live — Election Ongoing
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- PRINT RESULTS BUTTON REMOVED FOR VOTER --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('voter.dashboard') }}"
                           class="px-5 py-3 bg-slate-100 text-slate-700 rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-slate-200 transition-all">
                            ← Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- RESULTS GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 no-print">
                @foreach($results->positions as $position)
                    @php
                        $totalPosVotes = $position->candidates->sum('votes_count');
                        $maxVotes      = $position->candidates->max('votes_count') ?? 0;
                        $winner        = $position->candidates->sortByDesc('votes_count')->first();
                        $chartId       = 'chart_' . $position->id;
                    @endphp
                    <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-sm overflow-hidden flex flex-col">

                        {{-- Position Header --}}
                        <div class="bg-indigo-50 px-8 py-5 border-b border-indigo-100">
                            <h3 class="font-black text-indigo-900 uppercase text-sm tracking-widest">
                                🏷️ {{ $position->title }}
                            </h3>
                            <p class="text-[9px] text-indigo-400 font-bold uppercase tracking-widest mt-1">
                                Total Ballots: {{ number_format($totalPosVotes) }}
                            </p>
                        </div>

                        {{-- Bar Chart --}}
                        <div class="px-8 pt-6">
                            <canvas id="{{ $chartId }}" height="180"></canvas>
                        </div>

                        {{-- Candidates List --}}
                        <div class="p-8 space-y-4 flex-1">
                            @foreach($position->candidates->sortByDesc('votes_count') as $candidate)
                                @php
                                    $pct       = $totalPosVotes > 0 ? round(($candidate->votes_count / $totalPosVotes) * 100, 1) : 0;
                                    $isLeading = $candidate->votes_count === $maxVotes && $maxVotes > 0;
                                @endphp
                                <div class="flex items-center justify-between p-3 rounded-2xl {{ $isLeading ? 'bg-indigo-50 border border-indigo-100' : 'bg-slate-50' }}">
                                    <div>
                                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">
                                            {{ $candidate->party ?? 'Independent' }}
                                        </p>
                                        <p class="text-xs font-black text-slate-800 uppercase">
                                            {{ $candidate->first_name }} {{ $candidate->last_name }}
                                            @if($isLeading && $isElectionEnded)
                                                <span class="text-emerald-500 text-[9px] ml-1">★ WINNER</span>
                                            @elseif($isLeading && !$isElectionEnded)
                                                <span class="text-sky-400 text-[9px] ml-1">▲ LEADING</span>
                                            @endif
                                        </p>
                                        <p class="text-[8px] text-slate-400 font-bold uppercase mt-0.5">{{ $pct }}% Share</p>
                                    </div>
                                    <span class="text-2xl font-black {{ $isLeading ? 'text-indigo-600' : 'text-slate-400' }}">
                                        {{ number_format($candidate->votes_count) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Winner Banner --}}
                        @if($isElectionEnded && $winner && $winner->votes_count > 0)
                            <div class="px-8 pb-6">
                                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl px-5 py-3 flex items-center gap-3">
                                    <span class="text-lg">🏆</span>
                                    <div>
                                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Winner</p>
                                        <p class="text-sm font-black text-slate-900 uppercase">
                                            {{ $winner->first_name }} {{ $winner->last_name }}
                                        </p>
                                    </div>
                                    <div class="ml-auto text-right">
                                        <p class="text-xl font-black text-emerald-600">{{ number_format($winner->votes_count) }}</p>
                                        <p class="text-[8px] text-slate-400 uppercase">votes</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Chart.js Script per position --}}
                    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const isDark = localStorage.getItem('voterDarkMode') === 'true';

                        const ctx    = document.getElementById('{{ $chartId }}').getContext('2d');
                        const labels = @json($position->candidates->sortByDesc('votes_count')->pluck('first_name'));
                        const votes  = @json($position->candidates->sortByDesc('votes_count')->pluck('votes_count'));
                        const maxVote = Math.max(...votes);

                        const winnerColor = '{{ $isElectionEnded ? "#10b981" : "#6366f1" }}';
                        const loserColor  = isDark ? '#374151' : '#e2e8f0';
                        const colors      = votes.map(v => v === maxVote && maxVote > 0 ? winnerColor : loserColor);

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    data: votes,
                                    backgroundColor: colors,
                                    borderRadius: 8,
                                    borderSkipped: false,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: { label: ctx => ctx.parsed.y + ' votes' }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            precision: 0,
                                            font: { weight: 'bold' },
                                            color: isDark ? '#64748b' : '#94a3b8',
                                        },
                                        grid: { color: isDark ? '#252a3a' : '#f1f5f9' }
                                    },
                                    x: {
                                        ticks: {
                                            font: { weight: 'bold', size: 10 },
                                            color: isDark ? '#94a3b8' : '#475569',
                                        },
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    });
                    </script>
                @endforeach
            </div>

            {{-- PRINT-ONLY: OFFICIAL CERTIFICATE --}}
            <div class="only-print">
                <div class="flex items-center gap-6 mb-8 border-b-4 border-black pb-6">
                    <img src="{{ asset('images/school-logo.png') }}" class="w-20 h-20">
                    <div>
                        <h1 class="text-lg font-bold uppercase">Catalino D. Cerezo National High School</h1>
                        <h2 class="text-2xl font-black uppercase">
                            {{ $isElectionEnded ? 'Official Certificate of Canvass' : 'Live Tally Report' }}
                        </h2>
                    </div>
                </div>

                <div class="border-2 border-black p-4 text-center font-black uppercase mb-6">
                    {{ $results->title }} —
                    {{ $isElectionEnded ? 'Final Results' : 'Current Standing as of ' . now()->format('M d, Y h:i A') }}
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="text-align:left; width:25%;">Position</th>
                            <th style="text-align:left; width:55%;">{{ $isElectionEnded ? 'Winner' : 'Leading Candidate' }}</th>
                            <th style="text-align:center; width:20%;">Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results->positions as $pos)
                            @php
                                $sorted  = $pos->candidates->sortByDesc('votes_count');
                                $topVote = $sorted->first()?->votes_count ?? 0;
                                $leaders = $sorted->filter(fn($c) => $c->votes_count === $topVote && $topVote > 0);
                            @endphp
                            <tr>
                                <td class="font-bold uppercase">{{ $pos->title }}</td>
                                <td>
                                    @if($leaders->isEmpty())
                                        <span style="color:#888; font-style:italic;">No votes</span>
                                    @elseif($leaders->count() > 1)
                                        @foreach($leaders as $l)
                                            <div class="font-bold uppercase">• {{ $l->first_name }} {{ $l->last_name }} (TIE)</div>
                                        @endforeach
                                    @else
                                        <div class="font-bold uppercase">{{ $leaders->first()->first_name }} {{ $leaders->first()->last_name }}</div>
                                    @endif
                                </td>
                                <td style="text-align:center;" class="font-bold">{{ number_format($topVote) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($isElectionEnded)
                <div style="margin-top:80px; display:flex; justify-content:space-between; padding:0 50px;">
                    <div style="width:220px; text-align:center;">
                        <div style="border-top:2px solid black; margin-bottom:8px;"></div>
                        <span style="font-weight:900; font-size:11px; text-transform:uppercase;">Election Chairman</span>
                    </div>
                    <div style="width:220px; text-align:center;">
                        <div style="border-top:2px solid black; margin-bottom:8px;"></div>
                        <span style="font-weight:900; font-size:11px; text-transform:uppercase;">School Principal</span>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </main>

    <div class="no-print">
        @include('dashboards.voter-dashboard.layout.footer')
    </div>

    @if(!$isElectionEnded)
    <script>setTimeout(function () { location.reload(); }, 15000);</script>
    @endif

    <script>
        setInterval(async function () {
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
            } catch (e) {}
        }, 30000);

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
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-emerald-500 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-3 font-black uppercase text-sm tracking-widest whitespace-nowrap no-print">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-rose-500 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-3 font-black uppercase text-sm tracking-widest whitespace-nowrap no-print">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

</body>
</html>