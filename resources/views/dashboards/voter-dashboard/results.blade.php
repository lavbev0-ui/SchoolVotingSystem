<x-app-layout>
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $election->title }} - Results</h1>
            <p class="text-gray-600">Real-time updates</p>
        </div>
        <a href="{{ route('voter.dashboard') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 transition hover:bg-gray-300">
            &larr; Back to Dashboard
        </a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        @foreach($election->positions as $position)
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-xl font-bold text-blue-600">{{ $position->title }}</h2>
                
                <div class="space-y-4">
                    @foreach($position->candidates as $candidate)
                        @php
                            // Calculate percentage to avoid division by zero
                            $totalVotes = $position->candidates->sum('votes_count');
                            $percentage = $totalVotes > 0 ? ($candidate->votes_count / $totalVotes) * 100 : 0;
                        @endphp

                        <div>
                            <div class="mb-1 flex justify-between">
                                <span class="font-medium text-gray-800">{{ $candidate->name }}</span>
                                <span class="font-bold text-gray-900">{{ $candidate->votes_count }} Votes</span>
                            </div>
                            
                            {{-- Progress Bar --}}
                            <div class="h-4 w-full overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full bg-blue-500 transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="mt-1 text-right text-xs text-gray-500">
                                {{ number_format($percentage, 1) }}%
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
</x-app-layout>