<x-app-layout>
    {{-- Main Container --}}
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        {{-- Header / Navigation --}}
        <div class="mb-6">
            <a href="{{ route('dashboard.elections.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back to Elections
            </a>

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $election->title }}</h1>
                    <p class="mt-2 text-gray-600 text-lg">
                        {{ $election->bio ?? 'No description provided for this election.' }}
                    </p>
                </div>

                {{-- Status Badge --}}
                @php
                    $statusStyles = match($election->status) {
                        'active'    => 'bg-green-100 text-green-800 border-green-200',
                        'upcoming'  => 'bg-blue-100 text-blue-800 border-blue-200',
                        'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
                        'archived'  => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        default     => 'bg-gray-100 text-gray-800'
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusStyles }}">
                    {{ ucfirst($election->status) }}
                </span>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            
            {{-- Duration Card --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0h18M5.25 12h13.5" /></svg>
                    <h3 class="text-sm font-medium text-gray-900">Duration</h3>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Start</p>
                    <p class="font-semibold text-gray-900">{{ $election->start_at?->format('M d, Y g:i A') ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mt-2">End</p>
                    <p class="font-semibold text-gray-900">{{ $election->end_at?->format('M d, Y g:i A') ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Turnout Card --}}
            @php
                // 1. Get Votes Cast
                $totalVotes = $election->votes_count ?? 0;

                // 2. Decode eligibility
                $metadata = $election->eligibility_metadata;
                if (is_string($metadata)) {
                    $metadata = json_decode($metadata, true) ?? [];
                }

                // 3. Calculate Eligible Voters
                $totalEligible = 0;

                if ($election->eligibility_type === 'all') {
                    // FIX: Added \App\Models\ here
                    $totalEligible = \App\Models\Voter::where('is_active', true)->count(); 
                } 
                elseif ($election->eligibility_type === 'grade_level') {
                    // Handle the specific data structure (['grades' => [1,4...]])
                    $gradeIds = $metadata['grades'] ?? $metadata;

                    // FIX: Added \App\Models\ here
                    $totalEligible = \App\Models\Voter::where('is_active', true)
                                          ->whereIn('grade_level_id', $gradeIds)
                                          ->count();
                }

                // 4. Calculate Percentage
                $turnoutPercentage = $totalEligible > 0 ? number_format(($totalVotes / $totalEligible) * 100, 1) : 0;
            @endphp
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>
                    <h3 class="text-sm font-medium text-gray-900">Turnout</h3>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $turnoutPercentage }}%</p>
                <p class="text-sm text-gray-500 mt-1">{{ $totalVotes }} / {{ $totalEligible }} votes</p>
            </div>

            {{-- Positions Count Card --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0V5.625a2.25 2.25 0 10-4.5 0v5.772m0 0A2.25 2.25 0 0112 12a2.25 2.25 0 01-2.25-2.25" /></svg>
                    <h3 class="text-sm font-medium text-gray-900">Positions</h3>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $election->positions->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{-- Calculate total candidates dynamically --}}
                    {{ $election->positions->sum(fn($p) => $p->candidates->count()) }} total candidates
                </p>
            </div>

            {{-- Eligible Voters Card --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    <h3 class="text-sm font-medium text-gray-900">Eligible Voters</h3>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalEligible > 0 ? $totalEligible : '-' }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    @if($election->eligibility_type === 'all')
                        All students
                    @elseif($election->eligibility_type === 'grade_level')
                        {{-- Handle JSON metadata access cleanly --}}
                        @php
                            $grades = $election->eligibility_metadata['grades'] ?? [];
                            $grades = is_array($grades) ? implode(', ', $grades) : $grades;
                        @endphp
                        Grades: {{ $grades ?: 'None' }}
                    @elseif($election->eligibility_type === 'section')
                         @php
                            $sections = $election->eligibility_metadata['sections'] ?? [];
                            $sections = is_array($sections) ? implode(', ', $sections) : $sections;
                        @endphp
                        Sections: {{ $sections ?: 'None' }}
                    @else
                        Specific List
                    @endif
                </p>
            </div>
        </div>

        <hr class="border-gray-200 mb-8" />

        {{-- Positions & Candidates Tabs --}}
        {{-- Initialize x-data with the first position ID, handling case where no positions exist --}}
        <div x-data="{ currentTab: '{{ $election->positions->first()?->id ?? 0 }}' }">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Positions & Candidates</h3>

            @if($election->positions->count() > 0)
                {{-- Tabs Header --}}
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                        @foreach($election->positions as $position)
                            <button 
                                @click="currentTab = '{{ $position->id }}'"
                                :class="currentTab == '{{ $position->id }}' 
                                    ? 'border-indigo-500 text-indigo-600' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150">
                                {{ $position->title }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Tabs Content --}}
                @foreach($election->positions as $position)
                    <div x-show="currentTab == '{{ $position->id }}'" x-cloak class="space-y-6">
                        
                        {{-- Position Header Card --}}
                        <div class="bg-white shadow rounded-lg border border-gray-200 p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900">{{ $position->title }}</h2>
                                    <p class="mt-1 text-sm text-gray-500">{{ $position->description ?? 'No description provided' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    Max {{ $position->max_selection }} {{ Str::plural('winner', $position->max_selection) }}
                                </span>
                            </div>
                        </div>

                        {{-- Candidates Grid --}}
                        <div>
                            <h4 class="font-medium text-sm text-gray-500 mb-4">
                                Candidates ({{ $position->candidates->count() }})
                            </h4>

                            @if($position->candidates->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($position->candidates as $candidate)
                                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1">
                                                    <h5 class="text-base font-semibold text-gray-900">{{ $candidate->name }}</h5>
                                                    
                                                    <div class="flex flex-wrap gap-2 mt-2">
                                                        @if($candidate->gradeLevel)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                                {{ $candidate->gradeLevel->name }}
                                                            </span>
                                                        @endif
                                                        @if($candidate->section)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                                {{ $candidate->section->name }}
                                                            </span>
                                                        @endif
                                                        @if($candidate->party)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                                {{ $candidate->party }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Vote Count (Only if completed) --}}
                                                @if($election->status === 'completed')
                                                    <div class="text-center">
                                                        <p class="text-2xl font-bold text-indigo-600">{{ $candidate->votes_count ?? 0 }}</p>
                                                        <p class="text-xs text-gray-500">votes</p>
                                                    </div>
                                                @endif
                                            </div>

                                            @if($candidate->platform)
                                                <div class="mt-4 pt-4 border-t border-gray-100">
                                                    <p class="text-sm text-gray-600 italic">
                                                        "{{ $candidate->platform }}"
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No candidates added</h3>
                                    <p class="mt-1 text-sm text-gray-500">There are no candidates listed for this position yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-10">
                    <p class="text-gray-500">No positions have been created for this election yet.</p>
                </div>
            @endif
        </div>

        {{-- Footer Actions --}}
        @if($election->status === 'completed')
            <div class="mt-10 flex justify-end">
                <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Export Results
                </a>
            </div>
        @endif
    </div>
</x-app-layout>