<x-app-layout>
    @php $isFinished = now()->gt($election->end_at); @endphp
    <div class="py-6 bg-slate-50 min-h-screen"
         x-data="{ timer: '', openVoters: null }"
         x-init="@if(!$isFinished) setInterval(() => { window.location.reload() }, 10000) @endif">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-6">

            {{-- TOP NAVIGATION --}}
            <div class="flex items-center justify-between no-print mb-2">
                <a href="{{ route('admin.elections.index') }}" class="flex items-center gap-2 text-slate-400 hover:text-indigo-600 transition-colors group">
                    <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm border border-slate-100 group-hover:bg-indigo-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest">Back to Management</span>
                </a>
                <div x-data="countdown('{{ $election->end_at }}')" class="flex items-center gap-3 px-4 py-2 bg-white border border-slate-100 rounded-2xl shadow-sm">
                    <div class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></div>
                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest" x-text="timer">LOADING...</span>
                </div>
            </div>

            {{-- HEADER STATS --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden p-8">
                <div class="text-center">
                    @php $isFinished = now()->gt($election->end_at); @endphp
                    <span class="px-3 py-1 rounded-full text-[7px] font-black uppercase tracking-widest {{ $isFinished ? 'bg-slate-100 text-slate-500' : 'bg-indigo-50 text-indigo-600 animate-pulse border border-indigo-100' }}">
                        {{ $isFinished ? '● Final Tally Record' : '● Live processing' }}
                    </span>
                    <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter leading-none mt-4">{{ $election->title }}</h1>
                    <div class="flex flex-col items-center justify-center py-6 border-y border-slate-50/50 mt-6 font-mono">
                        <div class="flex items-baseline gap-3">
                            <span class="text-6xl font-black text-indigo-600 tracking-tighter leading-none">{{ number_format($election->voted_count ?? 0) }}</span>
                            <span class="text-[11px] font-black text-slate-300 uppercase tracking-[0.2em]">Participants</span>
                        </div>
                        <p class="text-[8px] font-black text-slate-900 uppercase tracking-[0.2em] mt-2 italic">
                            Verified out of {{ number_format($election->total_voters ?? 0) }} Total Voters
                        </p>
                    </div>
                </div>
            </div>

            {{-- LIVE TALLY GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($election->positions as $position)
                    @php
                        $candidates    = $position->candidates->sortByDesc('votes_count');
                        $totalPosVotes = $candidates->sum('votes_count');
                    @endphp

                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col p-6">
                        <h3 class="font-black text-slate-800 uppercase text-[10px] tracking-widest mb-8 flex items-center gap-3">
                            <div class="w-1.5 h-4 bg-indigo-600 rounded-full"></div>
                            {{ $position->title }}
                        </h3>

                        <div class="space-y-8">
                            @forelse($candidates as $candidate)
                                @php
                                    $percent = $totalPosVotes > 0 ? ($candidate->votes_count / $totalPosVotes) * 100 : 0;

                                    // Voters who voted for this candidate
                                    $votersForCandidate = \App\Models\Vote::where('candidate_id', $candidate->id)
                                        ->where('election_id', $election->id)
                                        ->with('voter')
                                        ->get()
                                        ->map(fn($v) => [
                                            'name'    => $v->voter ? $v->voter->first_name . ' ' . $v->voter->last_name : 'Unknown',
                                            'grade'   => $v->voter?->gradeLevel?->name ?? '—',
                                            'section' => $v->voter?->section?->name ?? '—',
                                        ]);
                                @endphp
                                <div class="relative">
                                    <div class="flex justify-between items-end mb-2">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-800 uppercase italic leading-none">{{ $candidate->full_name }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase mt-1">{{ $candidate->party ?? 'Independent' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-right">
                                                <span class="text-xl font-black text-indigo-600 leading-none block">{{ number_format($candidate->votes_count ?? 0) }}</span>
                                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-tighter">Votes</span>
                                            </div>
                                            {{-- View Voters Button --}}
                                            @if($candidate->votes_count > 0)
                                            <button
                                                @click="openVoters = openVoters === '{{ $candidate->id }}' ? null : '{{ $candidate->id }}'"
                                                class="text-[8px] font-black uppercase tracking-widest px-2 py-1 rounded-lg border border-indigo-100 bg-indigo-50 text-indigo-500 hover:bg-indigo-100 transition">
                                                Voters
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-50 h-4 rounded-full overflow-hidden border border-slate-100 shadow-inner">
                                        <div class="bg-indigo-500 h-full transition-all duration-1000 ease-out shadow-[0_0_15px_rgba(79,70,229,0.4)]"
                                             style="width: {{ $percent }}%"></div>
                                    </div>
                                    <div class="mt-1.5 flex justify-end">
                                        <span class="text-[8px] font-black text-slate-500 uppercase tracking-widest bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100">
                                            {{ number_format($percent, 1) }}% share
                                        </span>
                                    </div>

                                    {{-- Voters List Dropdown --}}
                                    <div x-show="openVoters === '{{ $candidate->id }}'"
                                         x-transition
                                         class="mt-3 bg-indigo-50 rounded-2xl border border-indigo-100 overflow-hidden">
                                        <div class="px-4 py-2 border-b border-indigo-100 bg-indigo-100">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">
                                                Voters who chose {{ $candidate->first_name }}
                                            </p>
                                        </div>
                                        <div class="divide-y divide-indigo-100 max-h-48 overflow-y-auto">
                                            @forelse($votersForCandidate as $voter)
                                            <div class="flex items-center justify-between px-4 py-2">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-indigo-200 flex items-center justify-center">
                                                        <span class="text-[9px] font-black text-indigo-700">{{ strtoupper(substr($voter['name'], 0, 1)) }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-black text-slate-700 uppercase">{{ $voter['name'] }}</span>
                                                </div>
                                                <span class="text-[9px] text-slate-400 font-bold">{{ $voter['grade'] }} — {{ $voter['section'] }}</span>
                                            </div>
                                            @empty
                                            <p class="text-center py-3 text-[9px] text-slate-400">No voters yet</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-[10px] text-slate-300 italic uppercase">No candidates registered</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- HISTORY LOG --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between mb-6 border-b pb-4">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        History Log
                    </h3>
                    <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 uppercase">
                        {{ $votedVotersList->count() }} Records
                    </span>
                </div>
                <div x-data="{ search: '' }">
                    <input type="text" x-model="search" placeholder="Search voter name..."
                           class="w-full mb-4 px-4 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    <div class="overflow-auto max-h-[400px]">
                        <table class="w-full text-left">
                            <thead class="sticky top-0 bg-white">
                                <tr class="border-b border-slate-100">
                                    <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">#</th>
                                    <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Voter</th>
                                    <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Grade & Section</th>
                                    <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($votedVotersList as $index => $voter)
                                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all"
                                    x-show="'{{ strtolower($voter['voter_name']) }}'.includes(search.toLowerCase())">
                                    <td class="py-3 pr-4"><span class="text-[9px] font-black text-slate-400">{{ $index + 1 }}</span></td>
                                    <td class="py-3 pr-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                                <span class="text-[9px] font-black text-indigo-600">{{ strtoupper(substr($voter['voter_name'], 0, 1)) }}</span>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-800 uppercase">{{ $voter['voter_name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 pr-4"><span class="text-[9px] font-bold text-slate-500">{{ $voter['grade'] }} — {{ $voter['section'] }}</span></td>
                                    <td class="py-3"><span class="text-[9px] font-bold text-slate-400">{{ $voter['voted_at'] }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">No votes recorded yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function countdown(expiry) {
            return {
                timer: '',
                expiryDate: new Date(expiry).getTime(),
                init() {
                    let loop = setInterval(() => {
                        let now = new Date().getTime();
                        let diff = this.expiryDate - now;
                        if (diff < 0) { this.timer = "ELECTION CLOSED"; clearInterval(loop); return; }
                        let h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        let m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                        let s = Math.floor((diff % (1000 * 60)) / 1000);
                        this.timer = `${h}H ${m}M ${s}S REMAINING`;
                    }, 1000);
                }
            }
        }
    </script>
</x-app-layout>