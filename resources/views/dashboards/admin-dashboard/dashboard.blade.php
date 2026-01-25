<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- STATS GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Total Elections --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium text-gray-900">Total Elections</h3>
                            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-gray-900">{{ $totalElections }}</div>
                            <p class="text-xs text-gray-500 mt-1">{{ $activeElectionsCount }} currently active</p>
                        </div>
                    </div>
                </div>

                {{-- Total Votes --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium text-gray-900">Total Votes</h3>
                            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalVotes) }}</div>
                            <p class="text-xs text-gray-500 mt-1">Across all elections</p>
                        </div>
                    </div>
                </div>

                {{-- Registered Students --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium text-gray-900">Registered Students</h3>
                            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalVoters) }}</div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($activeVoters) }} Eligible voters</p>
                        </div>
                    </div>
                </div>

                {{-- Voter Turnout --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6">
                        <div class="flex flex-row items-center justify-between pb-2">
                            <h3 class="text-sm font-medium text-gray-900">Voter Turnout</h3>
                            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <div class="mt-2">
                            <div class="text-2xl font-bold text-gray-900">{{ number_format($turnoutPercentage, 1) }}%</div>
                            <p class="text-xs text-gray-500 mt-1">Average participation rate</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LOWER SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Active Elections List --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Active Elections</h3>
                        <p class="text-sm text-gray-500">Currently ongoing elections</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        @forelse($activeElectionsList as $election)
                            @php
                                // Calculate simple progress percentage for this specific election
                                // Using total voters as the denominator (approximation)
                                $percent = $totalVoters > 0 ? ($election->votes_count / $totalVoters) * 100 : 0;
                            @endphp
                            
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $election->title }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $election->votes_count }} / {{ $totalVoters }} votes ({{ number_format($percent, 1) }}%)
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Ends: {{ \Carbon\Carbon::parse($election->end_at)->format('M d, Y') }}
                                    </p>
                                </div>
                                <a href="#" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                    View
                                </a>
                            </div>

                            @if(!$loop->last)
                                <div class="border-b border-gray-100"></div>
                            @endif
                        @empty
                            <div class="text-center text-gray-500 py-4">
                                No active elections at the moment.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Activity Feed --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        <p class="text-sm text-gray-500">Latest system updates</p>
                    </div>

                    <div class="p-6 space-y-6">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-start gap-3">
                                {{-- Determine Icon/Color based on model type --}}
                                @if($activity instanceof \App\Models\Vote)
                                    <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 shrink-0"></div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm text-gray-900">New Vote Cast</p>
                                        <p class="text-xs text-gray-500">
                                            For {{ $activity->election->title ?? 'Unknown Election' }}
                                        </p>
                                    </div>
                                @elseif($activity instanceof \App\Models\Election)
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2 shrink-0"></div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm text-gray-900">Election Created</p>
                                        <p class="text-xs text-gray-500">{{ $activity->title }}</p>
                                    </div>
                                @endif
                                
                                <p class="text-xs text-gray-400 mt-1 whitespace-nowrap">
                                    {{ $activity->created_at->diffForHumans(null, true, true) }}
                                </p>
                            </div>

                            @if(!$loop->last)
                                <div class="border-b border-gray-100"></div>
                            @endif
                        @empty
                            <div class="text-center text-gray-500 py-4">
                                No recent activity found.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>