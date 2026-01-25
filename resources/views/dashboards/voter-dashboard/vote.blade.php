<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vote: {{ $election->title }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased" 
      x-data="votingSystem({{ json_encode($election->positions) }})">

    <nav class="sticky top-0 z-30 w-full bg-white/80 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold leading-tight tracking-tight text-gray-900">Voting System</h1>
                        <p class="text-xs font-medium text-gray-500">Logged in as {{ Auth::user()->name ?? 'Student' }}</p>
                    </div>
                </div>

                <a href="{{ route('voter.dashboard') }}" 
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
        
        <div class="h-1 w-full bg-gray-100">
            <div class="h-1 bg-indigo-600 transition-all duration-500 ease-out" :style="'width: ' + progressPercentage + '%'"></div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-10 text-center sm:text-left sm:flex sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ $election->title }}</h2>
                    <p class="mt-2 text-lg text-gray-600">Please review candidates carefully before submitting your vote.</p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-gray-500">Progress</p>
                        <p class="text-xl font-bold text-indigo-600">
                            <span x-text="voteCount"></span> / <span x-text="totalPositions"></span>
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                        Active Election
                    </span>
                </div>
            </div>

            <form id="votingForm" action="{{ route('voter.vote.store') }}" method="POST">
                @csrf
                <input type="hidden" name="election_id" value="{{ $election->id }}">

                <div class="space-y-16">
                    @foreach($election->positions as $position)
                        <section id="position-{{ $position->id }}" class="scroll-mt-32 relative">
                            
                            <div class="mb-6 flex items-center gap-4 border-l-4 border-indigo-600 pl-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $position->title }}</h3>
                                    <p class="text-sm text-gray-500">
                                        Select <span class="font-bold text-gray-800">{{ $position->max_selection }}</span> {{ Str::plural('candidate', $position->max_selection) }}
                                    </p>
                                </div>
                                <div x-show="isPositionFilled({{ $position->id }})" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 scale-50"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="ml-auto rounded-full bg-green-100 p-1 text-green-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-6">
                                @forelse($position->candidates as $candidate)
                                    <div @click="toggleVote({{ $position->id }}, {{ $candidate->id }}, {{ $position->max_selection }}, '{{ addslashes($candidate->name) }}')"
                                         role="button"
                                         tabindex="0"
                                         :class="isSelected({{ $position->id }}, {{ $candidate->id }}) 
                                            ? 'ring-2 ring-indigo-600 border-indigo-600 bg-indigo-50' 
                                            : 'border-gray-200 bg-white hover:border-indigo-300 hover:shadow-md'"
                                         class="group relative overflow-hidden rounded-xl border transition-all duration-200 focus:outline-none">

                                        @if($position->max_selection > 1)
                                            <input type="checkbox" name="votes[{{ $position->id }}][]" value="{{ $candidate->id }}" class="hidden" :checked="isSelected({{ $position->id }}, {{ $candidate->id }})">
                                        @else
                                            <input type="radio" name="votes[{{ $position->id }}]" value="{{ $candidate->id }}" class="hidden" :checked="isSelected({{ $position->id }}, {{ $candidate->id }})">
                                        @endif

                                        <div class="flex flex-col md:flex-row">
                                            <div class="md:w-64 flex-shrink-0 bg-gray-100 md:border-r border-gray-200">
                                                <div class="h-64 md:h-full w-full relative">
                                                    @if($candidate->photo_path)
                                                        <img src="{{ asset('storage/' . $candidate->photo_path) }}" alt="{{ $candidate->name }}" class="absolute inset-0 h-full w-full object-cover">
                                                    @else
                                                        <div class="absolute inset-0 flex items-center justify-center bg-indigo-100 text-6xl font-bold text-indigo-400">
                                                            {{ substr($candidate->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    
                                                    <div x-show="isSelected({{ $position->id }}, {{ $candidate->id }})" 
                                                         class="absolute top-4 right-4 bg-indigo-600 text-white rounded-full p-2 md:hidden shadow-lg">
                                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-1 p-6 md:p-8">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h4 class="text-3xl font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">
                                                            {{ $candidate->name }}
                                                        </h4>
                                                        
                                                        <div class="mt-2 flex flex-wrap gap-2 text-sm text-gray-600">
                                                            @if($candidate->party)
                                                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 font-medium text-gray-700 ring-1 ring-inset ring-gray-600/10">
                                                                    {{ $candidate->party }}
                                                                </span>
                                                            @endif

                                                            <span class="inline-flex items-center rounded-md px-2 py-1 font-medium text-gray-500">
                                                                {{ $candidate->gradeLevel?->name ?? 'Student' }} 
                                                                @if($candidate->section?->name) &bull; {{ $candidate->section->name }} @endif
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div :class="isSelected({{ $position->id }}, {{ $candidate->id }}) ? 'bg-indigo-600 border-indigo-600' : 'bg-white border-gray-300'"
                                                         class="hidden md:flex h-10 w-10 flex-shrink-0 rounded-full border transition-colors duration-200 items-center justify-center mt-1">
                                                        <svg x-show="isSelected({{ $position->id }}, {{ $candidate->id }})" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                </div>

                                                @if($candidate->platform || $candidate->bio)
                                                    <div class="mt-6">
                                                        <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Platform & Goals</h5>
                                                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-100">
                                                            <p class="text-base leading-relaxed text-gray-700 whitespace-pre-wrap font-medium">
                                                                {{ $candidate->platform ?? $candidate->bio }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @else 
                                                    <div class="mt-6 text-gray-400 italic text-sm">
                                                        No platform information provided.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center rounded-xl border-2 border-dashed border-gray-200 bg-gray-50">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No candidates listed for this position.</p>
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="mt-16 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-10">
                    <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                        <div class="text-center sm:text-left">
                            <h3 class="text-lg font-bold text-gray-900">Ready to cast your vote?</h3>
                            <p class="text-sm text-gray-500">Please review your selections. You cannot change your vote after submitting.</p>
                        </div>
                        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                            <button type="button" onclick="window.history.back()" 
                                    class="inline-flex justify-center items-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Cancel
                            </button>
                            <button type="button" 
                                    @click="prepareConfirmation()"
                                    :disabled="!isFormComplete"
                                    :class="!isFormComplete ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200'"
                                    class="inline-flex justify-center items-center rounded-lg px-6 py-3 text-sm font-semibold text-white shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                                <span x-show="!isFormComplete" class="mr-2">Complete Selection to Submit</span>
                                <span x-show="isFormComplete">Submit Final Vote</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer class="mt-auto border-t bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-sm font-semibold text-gray-900">CATALINO D. CEREZO NATIONAL HIGH SCHOOL</p>
            <p class="mt-1 text-sm text-gray-500">&copy; {{ date('Y') }} Secure Student Voting System. All rights reserved.</p>
        </div>
    </footer>

    <div x-show="showConfirmModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <div x-show="showConfirmModal" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showConfirmModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">Confirm Submission</h3>
                                <p class="mt-2 text-sm text-gray-500">You are about to cast your vote. Please review your choices below.</p>
                                
                                <div class="mt-4 max-h-[50vh] overflow-y-auto rounded-lg border border-gray-100 bg-gray-50">
                                    <ul role="list" class="divide-y divide-gray-100">
                                        <template x-for="pos in positionsData" :key="pos.id">
                                            <li class="flex items-center justify-between py-3 px-4">
                                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500" x-text="pos.title"></span>
                                                <span class="text-sm font-medium text-gray-900 text-right" x-text="getSelectedName(pos.id)"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" @click="submitForm()" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Confirm & Vote</button>
                        <button type="button" @click="showConfirmModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Go Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('votingSystem', (positions) => ({
                votes: {},
                names: {},
                positionsData: positions,
                showConfirmModal: false,

                get totalPositions() { return this.positionsData.length; },
                
                get voteCount() { 
                    return Object.keys(this.votes).filter(k => this.votes[k].length > 0).length; 
                },
                
                get isFormComplete() { return this.voteCount === this.totalPositions; },

                get progressPercentage() {
                    return (this.voteCount / this.totalPositions) * 100;
                },

                toggleVote(posId, candId, maxAllowed, candName) {
                    if (!this.votes[posId]) this.votes[posId] = [];
                    this.names[candId] = candName; 

                    const index = this.votes[posId].indexOf(candId);

                    if (maxAllowed === 1) {
                        this.votes[posId] = [candId]; // Radio behavior
                    } else {
                        if (index === -1) {
                            if (this.votes[posId].length < maxAllowed) {
                                this.votes[posId].push(candId);
                            } else {
                                alert(`Limit reached: You can only choose ${maxAllowed} candidates.`);
                            }
                        } else {
                            this.votes[posId].splice(index, 1);
                        }
                    }
                },

                isSelected(posId, candId) {
                    return this.votes[posId] && this.votes[posId].includes(candId);
                },

                isPositionFilled(posId) {
                    return this.votes[posId] && this.votes[posId].length > 0;
                },

                getSelectedName(posId) {
                    if (!this.votes[posId] || this.votes[posId].length === 0) return 'Skipped / None';
                    return this.votes[posId].map(id => this.names[id]).join(', ');
                },

                prepareConfirmation() {
                    if (this.isFormComplete) {
                        this.showConfirmModal = true;
                    } else {
                        // Find first unvoted position and scroll to it
                        const firstEmpty = this.positionsData.find(p => !this.votes[p.id] || this.votes[p.id].length === 0);
                        if(firstEmpty) {
                            const el = document.getElementById(`position-${firstEmpty.id}`);
                            if(el) {
                                el.scrollIntoView({behavior: 'smooth', block: 'center'});
                                el.classList.add('animate-pulse');
                                setTimeout(() => el.classList.remove('animate-pulse'), 1000);
                            }
                        }
                    }
                },

                submitForm() {
                    document.getElementById('votingForm').submit();
                }
            }));
        });
    </script>
</body>
</html>