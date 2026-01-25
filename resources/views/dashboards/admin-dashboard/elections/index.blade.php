<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- HEADER --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Manage Elections</h2>
                    <p class="text-sm text-gray-500">Create and manage all elections</p>
                </div>
                
                <a href="{{ route('dashboard.elections.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" wire:navigate>
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Create Election
                </a>
            </div>

            {{-- LIST OF ELECTIONS --}}
            <div class="space-y-4">
                @forelse ($elections as $election)
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                        
                        {{-- Card Header --}}
                        <div class="p-6 border-b border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $election->title }}</h3>
                                        
                                        @php
                                            $statusClasses = match($election->status) {
                                                'active' => 'bg-green-100 text-green-700 border border-green-200',
                                                'completed' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                                'upcoming' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                                'archived' => 'bg-gray-100 text-gray-600 border border-gray-200',
                                                default => 'bg-gray-100 text-gray-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }} capitalize">
                                            {{ $election->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        {{ $election->start_at->format('M d, Y h:i A') }} - 
                                        {{ $election->end_at->format('M d, Y h:i A') }}
                                    </p>
                                </div>

                                {{-- ACTION BUTTONS --}}
                                <div class="flex gap-2 items-center">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('dashboard.elections.edit', $election->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-medium text-xs text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                        Edit
                                    </a>
                                    
                                    {{-- View Button --}}
                                    <a href="{{ route('dashboard.elections.show', $election->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-medium text-xs text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                        View
                                    </a>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('dashboard.elections.destroy', $election->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this election? All associated votes and candidates will be permanently deleted.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-300 rounded-md font-medium text-xs text-red-700 shadow-sm hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Card Stats --}}
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Positions</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $election->positions_count }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Candidates</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $election->candidates_count }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Votes Cast</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $election->votes_count }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Turnout (Approx)</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        @if(isset($totalVoters) && $totalVoters > 0)
                                            {{ round(($election->votes_count / $totalVoters) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                @empty
                    {{-- EMPTY STATE --}}
                    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No elections found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new election.</p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard.elections.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Create Election
                            </a>
                        </div>
                    </div>
                @endforelse
                
                {{-- Pagination Links (if you used paginate in controller) --}}
                @if(method_exists($elections, 'links'))
                    <div class="mt-4">
                        {{ $elections->links() }}
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>