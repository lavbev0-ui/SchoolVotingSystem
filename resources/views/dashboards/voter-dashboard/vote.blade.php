<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cast Ballot | Enhance Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }

        .candidate-card { transition: all 0.2s ease; }
        .candidate-card.selected { border-color: #0ea5e9 !important; background: #f0f9ff !important; box-shadow: 0 4px 16px rgba(14,165,233,0.2) !important; }
        .candidate-card:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(0,0,0,0.08); }

        /* ================================================================
           DARK MODE — Cast Ballot Page
           ================================================================ */
        html.dark-mode body {
            background-color: #0f1117 !important;
            color: #e2e8f0 !important;
        }

        /* Base background */
        html.dark-mode [style*="background: #f8fafc"],
        html.dark-mode [style*="background:#f8fafc"] {
            background: #0f1117 !important;
        }

        /* Navbar */
        html.dark-mode nav { background-color: #12151f !important; }
        html.dark-mode .sticky { background-color: #12151f !important; }

        /* Progress card — yung light blue box */
        html.dark-mode [style*="background:#e0f2fe"],
        html.dark-mode [style*="background: #e0f2fe"] {
            background: #082f49 !important;
            border-color: #0369a1 !important;
        }

        /* Progress dots inner track */
        html.dark-mode .bg-slate-100 { background-color: #252a3a !important; }

        /* Position header card */
        html.dark-mode [style*="border:2px solid #bae6fd"],
        html.dark-mode [style*="border: 2px solid #bae6fd"] {
            background-color: #1e2130 !important;
            border-color: #0369a1 !important;
        }

        /* Candidate cards */
        html.dark-mode .candidate-card {
            background-color: #1e2130 !important;
            border-color: #1e4f6b !important;
        }
        html.dark-mode .candidate-card.selected {
            background: #082f49 !important;
            border-color: #0ea5e9 !important;
        }

        /* Candidate photo border */
        html.dark-mode [style*="border:2px solid #e2e8f0"] {
            border-color: #374151 !important;
            background: #252a3a !important;
        }

        /* Party badge */
        html.dark-mode .bg-sky-100  { background-color: #082f49 !important; }
        html.dark-mode .bg-slate-100 { background-color: #252a3a !important; }
        html.dark-mode .text-slate-500 { color: #94a3b8 !important; }

        /* Candidate name & text */
        html.dark-mode .text-slate-900 { color: #f1f5f9 !important; }
        html.dark-mode .text-slate-800 { color: #e2e8f0 !important; }
        html.dark-mode .text-slate-400 { color: #64748b !important; }

        /* Radio button outer ring */
        html.dark-mode .border-slate-300 { border-color: #4b5563 !important; }

        /* Back navigation button */
        html.dark-mode [style*="border-color:#bae6fd"] {
            border-color: #0369a1 !important;
            color: #7dd3fc !important;
            background-color: #1e2130 !important;
        }

        /* Vote summary box */
        html.dark-mode [style*="background:#bae6fd"],
        html.dark-mode [style*="background: #bae6fd"] {
            background: #0c4a6e !important;
            border-color: #0369a1 !important;
        }
        html.dark-mode .border-slate-50 { border-color: #2d3347 !important; }

        /* Modals */
        html.dark-mode .bg-white.rounded-t-3xl,
        html.dark-mode .bg-white.rounded-3xl {
            background-color: #1e2130 !important;
        }
        html.dark-mode .bg-slate-50 { background-color: #252a3a !important; }
        html.dark-mode .bg-indigo-50 { background-color: #1e1a3a !important; }
        html.dark-mode .text-slate-600 { color: #cbd5e1 !important; }
        html.dark-mode .text-indigo-800 { color: #c7d2fe !important; }
        html.dark-mode .text-slate-400 { color: #64748b !important; }

        /* Review modal ballot items */
        html.dark-mode .border-sky-200.bg-sky-50 {
            background-color: #082f49 !important;
            border-color: #0369a1 !important;
        }
        html.dark-mode .border-slate-100.bg-slate-50 {
            background-color: #1a1f2e !important;
            border-color: #2d3347 !important;
        }
        html.dark-mode .border-slate-200 { border-color: #374151 !important; }
        html.dark-mode .text-slate-600 { color: #cbd5e1 !important; }

        /* Images — no filter change */
        html.dark-mode img { filter: none !important; }
    </style>

    {{-- Apply dark mode instantly (no flash) --}}
    <script>
        if (localStorage.getItem('voterDarkMode') === 'true') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
</head>

<body class="min-h-screen" style="background: #f8fafc;">

    <div class="sticky top-0 z-40">
        @include('dashboards.voter-dashboard.layout.nav')
    </div>

    <main x-data="votingFlow()">

        {{-- PROFILE MODAL --}}
        <div x-show="showModal" x-cloak x-transition
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-0 sm:p-4"
             @click.self="showModal = false">
            <div class="bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl w-full sm:max-w-md overflow-hidden">
                <div class="relative h-24 bg-gradient-to-r from-sky-400 to-blue-400">
                    <button @click="showModal = false" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="absolute -bottom-8 left-6">
                        <div class="w-16 h-16 rounded-2xl border-4 border-white overflow-hidden shadow-lg bg-indigo-100">
                            <img x-show="modalCandidate.photo" :src="modalCandidate.photo" class="w-full h-full object-cover object-top">
                            <div x-show="!modalCandidate.photo" class="w-full h-full flex items-center justify-center text-indigo-500 font-black text-2xl uppercase" x-text="modalCandidate.initials"></div>
                        </div>
                    </div>
                </div>
                <div class="pt-12 px-6 pb-2">
                    <span class="inline-block px-3 py-1 bg-sky-100 text-sky-600 text-[10px] font-black uppercase tracking-widest rounded-full" x-text="modalCandidate.party"></span>
                    <p class="text-lg font-black text-slate-900 uppercase mt-1" x-text="modalCandidate.name"></p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest" x-text="modalCandidate.position"></p>
                </div>
                <div class="px-6 pb-6 space-y-4 max-h-[50vh] overflow-y-auto">
                    <div class="bg-slate-50 rounded-2xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Profile & Achievements</p>
                        <p class="text-sm text-slate-600 leading-relaxed" x-text="modalCandidate.bio || 'No profile provided.'"></p>
                    </div>
                    <div class="bg-indigo-50 rounded-2xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-indigo-400 mb-2">Campaign Platform</p>
                        <p class="text-sm text-indigo-800 leading-relaxed" x-text="modalCandidate.manifesto || 'No platform provided.'"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- REVIEW MODAL --}}
        <div x-show="showReview" x-cloak x-transition
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="bg-gradient-to-r from-sky-500 to-blue-500 px-6 py-5">
                    <p class="text-sky-100 text-[10px] font-black uppercase tracking-widest">Review Your Ballot</p>
                    <h3 class="text-white font-black text-lg uppercase mt-1">Confirm Before Submitting</h3>
                </div>
                <div class="p-6 space-y-3 max-h-[60vh] overflow-y-auto">
                    <template x-for="pos in positions" :key="pos.id">
                        <div class="flex items-center justify-between p-3 rounded-2xl border-2"
                             :class="votes[pos.id] ? 'border-sky-200 bg-sky-50' : 'border-slate-100 bg-slate-50'">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="pos.title"></p>
                                <p class="text-sm font-black text-slate-800 uppercase mt-0.5"
                                   x-text="votes[pos.id] ? getCandidateName(pos.id, votes[pos.id]) : 'SKIPPED'"></p>
                            </div>
                            <div class="w-7 h-7 rounded-full flex items-center justify-center"
                                 :class="votes[pos.id] ? 'bg-sky-400' : 'bg-slate-300'">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                          :d="votes[pos.id] ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'"/>
                                </svg>
                            </div>
                        </div>
                    </template>
                    <p class="text-[10px] text-slate-400 text-center italic mt-2">Skipped positions will not have a vote recorded.</p>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button @click="showReview = false"
                            class="flex-1 py-3 border-2 border-slate-200 text-slate-600 rounded-2xl font-black uppercase text-[11px] tracking-widest hover:bg-slate-50 transition">
                        ← Edit Votes
                    </button>
                    <button @click="confirmSubmit()"
                            :disabled="isSubmitting"
                            class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-2xl font-black uppercase text-[11px] tracking-widest shadow-lg transition disabled:opacity-50"
                            x-text="isSubmitting ? 'Submitting...' : '✓ Submit Ballot'">
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-lg mx-auto py-6 px-4">

            {{-- HEADER --}}
            <div class="mb-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-1">{{ $election->title }}</p>
                <h1 class="text-2xl font-black text-slate-900">Cast Your Ballot</h1>
            </div>

            {{-- PROGRESS --}}
            <div class="rounded-2xl p-5 mb-6" style="background:#e0f2fe; border:1px solid #bae6fd;">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Progress</p>
                        <p class="text-sm font-black text-slate-800">Position <span x-text="currentStep + 1"></span> of <span x-text="totalSteps"></span></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-sky-400 flex items-center justify-center">
                        <span class="text-white font-black text-sm" x-text="`${Math.round(((currentStep + 1) / totalSteps) * 100)}%`"></span>
                    </div>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-sky-400 to-blue-400 h-full rounded-full transition-all duration-500"
                         :style="`width: ${((currentStep + 1) / totalSteps) * 100}%`"></div>
                </div>
                <div class="flex justify-between mt-3">
                    <template x-for="(pos, idx) in positions" :key="idx">
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-2 h-2 rounded-full transition-all"
                                 :class="votes[pos.id] ? 'bg-sky-400' : idx === currentStep ? 'bg-indigo-400 ring-2 ring-indigo-200' : 'bg-slate-200'"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- BALLOT --}}
            <form method="POST" action="{{ route('voter.vote.store', $election->id) }}" @submit.prevent="openReview()">
                @csrf

                <template x-if="currentPosition">
                    <div>
                        {{-- Position Header --}}
                        <div class="rounded-2xl p-5 mb-4 bg-white" style="border:2px solid #bae6fd;">
                            <div>
                                <p class="text-sky-500 text-[10px] font-black uppercase tracking-widest mb-1">Vote for</p>
                                <h2 class="text-slate-900 font-black text-xl uppercase" x-text="currentPosition.title"></h2>
                                <p class="text-slate-400 text-[10px] mt-1" x-text="`${currentPosition.candidates.length} candidate(s)`"></p>
                            </div>
                        </div>

                        {{-- Candidates --}}
                        <div class="space-y-3 mb-6">
                            <template x-for="candidate in currentPosition.candidates" :key="candidate.id">
                                <div class="candidate-card rounded-2xl border-2 overflow-hidden bg-white" style="border-color:#bae6fd;"
                                     :class="{'selected': votes[currentPosition.id] == candidate.id}">
                                    <div class="flex items-center p-4 gap-4">
                                        <div style="width:64px;height:64px;min-width:64px;border-radius:14px;overflow:hidden;border:2px solid #e2e8f0;background:#f1f5f9;flex-shrink:0;">
                                            <template x-if="candidate.photo_path">
                                                <img :src="'/storage/' + candidate.photo_path" style="width:64px;height:64px;object-fit:cover;object-position:top;display:block;">
                                            </template>
                                            <template x-if="!candidate.photo_path">
                                                <div style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#6366f1;text-transform:uppercase;">
                                                    <span x-text="candidate.first_name ? candidate.first_name.charAt(0) : '?'"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide mb-1"
                                                  :class="candidate.party ? 'bg-sky-100 text-sky-600' : 'bg-slate-100 text-slate-500'"
                                                  x-text="candidate.party || 'Independent'"></span>
                                            <p class="font-black text-slate-900 text-sm uppercase leading-tight truncate" x-text="candidate.full_name"></p>
                                            <button type="button" @click.stop="showCandidateProfile(candidate)"
                                                    class="text-[10px] font-bold text-sky-400 hover:text-sky-600 mt-1 flex items-center gap-1 transition-colors">
                                                View Profile
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>
                                        <button type="button" @click="selectCandidate(candidate.id)"
                                                class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center transition-all hover:bg-sky-50">
                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                                 :class="votes[currentPosition.id] == candidate.id ? 'border-sky-400 bg-sky-400' : 'border-slate-300'">
                                                <div x-show="votes[currentPosition.id] == candidate.id" class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                            </div>
                                        </button>
                                    </div>
                                    <div x-show="votes[currentPosition.id] == candidate.id" class="h-1 bg-gradient-to-r from-sky-400 to-blue-400"></div>
                                </div>
                            </template>
                        </div>

                        {{-- Navigation --}}
                        <div class="flex gap-3">
                            <button type="button" @click="prevStep()" :disabled="currentStep === 0"
                                    class="flex-1 py-4 border-2 rounded-2xl font-black uppercase text-[11px] tracking-widest transition disabled:opacity-30 disabled:cursor-not-allowed bg-white"
                                    style="border-color:#bae6fd; color:#0369a1;">
                                ← Back
                            </button>

                            <template x-if="currentStep < totalSteps - 1">
                                <button type="button" @click="nextStep()"
                                        class="flex-1 py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest transition bg-gradient-to-r from-sky-500 to-blue-400 text-white shadow-lg shadow-sky-200">
                                    Next →
                                </button>
                            </template>

                            <template x-if="currentStep === totalSteps - 1">
                                <button type="submit"
                                        :disabled="isSubmitting"
                                        class="flex-1 py-4 rounded-2xl font-black uppercase text-[11px] tracking-widest transition bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-200 disabled:opacity-50">
                                    Review & Submit →
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Vote Summary --}}
                <template x-if="Object.keys(votes).length > 0">
                    <div class="mt-6 rounded-2xl overflow-hidden" style="background:#e0f2fe; border:1px solid #bae6fd;">
                        <div class="px-5 py-3 border-b" style="background:#bae6fd; border-color:#7dd3fc;">
                            <p class="text-[10px] font-black uppercase tracking-widest text-sky-700">📋 Your Votes So Far</p>
                        </div>
                        <div class="p-4 space-y-2">
                            <template x-for="(candidateId, posId) in votes" :key="posId">
                                <div class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase truncate mr-2" x-text="getPositionName(posId)"></span>
                                    <span class="text-[11px] font-black text-sky-600 uppercase text-right truncate" x-text="getCandidateName(posId, candidateId)"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

            </form>
        </div>
    </main>

    @include('dashboards.voter-dashboard.layout.footer')

    <script>
        const electionData = @json($election);
        const submitUrl = "{{ route('voter.vote.store', $election->id) }}";
        const dashboardUrl = "{{ route('voter.dashboard') }}";

        function votingFlow() {
            return {
                currentStep: 0,
                votes: {},
                showModal: false,
                showReview: false,
                modalCandidate: {},
                isSubmitting: false,

                get positions() { return electionData.positions || []; },
                get totalSteps() { return this.positions.length; },
                get currentPosition() { return this.positions[this.currentStep]; },
                get hasVotedForCurrentPosition() {
                    return this.currentPosition && this.votes[this.currentPosition.id];
                },

                selectCandidate(candidateId) {
                    if (this.currentPosition) this.votes[this.currentPosition.id] = candidateId;
                },

                nextStep() {
                    if (this.currentStep < this.totalSteps - 1) {
                        this.currentStep++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                prevStep() {
                    if (this.currentStep > 0) {
                        this.currentStep--;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                openReview() { this.showReview = true; },

                showCandidateProfile(candidate) {
                    this.modalCandidate = {
                        name: candidate.full_name,
                        party: candidate.party || 'Independent',
                        position: this.currentPosition.title,
                        bio: candidate.bio || '',
                        manifesto: candidate.manifesto || '',
                        photo: candidate.photo_path ? '/storage/' + candidate.photo_path : '',
                        initials: candidate.first_name ? candidate.first_name.charAt(0) : '?'
                    };
                    this.showModal = true;
                },

                async confirmSubmit() {
                    this.isSubmitting = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const response = await fetch(submitUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ votes: this.votes }),
                        });
                        const data = await response.json();
                        window.location.href = data.redirect || dashboardUrl;
                    } catch (error) {
                        alert('Submission failed. Please try again.');
                        this.isSubmitting = false;
                    }
                },

                getPositionName(posId) {
                    const pos = this.positions.find(p => p.id == posId);
                    return pos?.title || 'Position';
                },

                getCandidateName(posId, candId) {
                    const pos = this.positions.find(p => p.id == posId);
                    if (!pos) return 'Unknown';
                    const cand = pos.candidates.find(c => c.id == candId);
                    return cand ? (cand.first_name + ' ' + cand.last_name) : 'Unknown';
                }
            };
        }

        /* Dark mode icon sync */
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