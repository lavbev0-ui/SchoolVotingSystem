<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Election Results</h2>
                <p class="text-sm text-gray-500">View detailed results and analytics</p>
            </div>

            {{-- SECTION 1: COMPLETED ELECTIONS --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Completed Elections</h3>
                    <p class="text-sm text-gray-500">View results from completed elections</p>
                </div>

                <div class="p-6 space-y-6">
                    @php
                        // Filter for completed elections directly in the view if not done in controller
                        $completedElections = $elections->where('status', 'completed');
                    @endphp

                    @forelse ($completedElections as $election)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-gray-100 last:border-0 last:pb-0">
                            
                            {{-- Election Info --}}
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $election->title }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Total Votes: {{ $election->votes_count }} 
                                    (
                                    @if(isset($totalVoters) && $totalVoters > 0)
                                        {{ round(($election->votes_count / $totalVoters) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                    turnout)
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Completed: {{ \Carbon\Carbon::parse($election->end_at)->format('M d, Y') }}
                                </p>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3">
                                <a href="{{ route('dashboard.results.show', $election->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-medium text-xs text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    View Results
                                </a>
                                
                                {{-- Optional Export Button --}}
                                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-medium text-xs text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                    Export
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">No completed elections found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SECTION 2: ANALYTICS (Placeholder) --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Active Election Analytics</h3>
                    <p class="text-sm text-gray-500">Real-time voting statistics</p>
                </div>

                <div class="p-6">
                    <div class="text-center py-12 text-gray-400">
                        {{-- BarChart Icon --}}
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        <p class="text-sm">Detailed analytics and charts coming soon</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>