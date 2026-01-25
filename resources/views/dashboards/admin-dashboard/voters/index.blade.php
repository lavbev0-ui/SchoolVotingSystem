<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Manage Voters</h2>
                <p class="text-sm text-gray-500">View and manage registered voters</p>
            </div>
            
            <a href="{{ route('dashboard.voters.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 gap-2">
                {{-- PlusCircle Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Add Voter
            </a>
        </div>

        {{-- Main Content Card --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            
            {{-- Card Header --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Registered Voters</h3>
                <p class="text-sm text-gray-500">All registered voters in the system</p>
            </div>

            {{-- Card Content --}}
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($voters as $voter)
                        <div class="flex flex-col md:flex-row md:items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors gap-4">
                            
                            {{-- Left Side: User Info --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    {{-- voter image --}}
                                    <div class="w-10 h-10 flex-shrink-0">
                                        @if($voter->photo_path)
                                            {{-- Display uploaded image --}}
                                            <img src="{{ Storage::url($voter->photo_path) }}" 
                                                alt="{{ $voter->first_name }}" 
                                                class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                        @else
                                            {{-- Fallback to default Icon if no image --}}
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $voter->full_name }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                            {{-- User ID Badge --}}
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                ID: {{ $voter->userID ?? 'N/A' }}
                                            </span>
                                            {{-- Grade Badge --}}
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $voter->gradeLevel->name ?? 'No Grade' }}
                                            </span>
                                            {{-- Section Badge (Optional/Placeholder) --}}
                                            @if(isset($voter->section))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                    {{ $voter->section->name ?? 'No Section' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Side: Stats & Actions --}}
                            <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">
                                
                                {{-- Voting Stats --}}
                                <div class="text-right mr-2 hidden sm:block">
                                    <p class="text-xs text-gray-500">Voted in</p>
                                    <p class="font-semibold text-gray-900 text-sm">
                                        {{-- Assuming you have a relationship setup or count available --}}
                                        {{ $voter->votes_count ?? 0 }} 
                                        election{{ ($voter->votes_count ?? 0) !== 1 ? 's' : '' }}
                                    </p>
                                </div>

                                {{-- Status Badge --}}
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $voter->is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-yellow-100 text-yellow-800 border-yellow-200' }}">
                                    {{ $voter->is_active ? 'Active' : 'Pending' }}
                                </span>

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-1">
                                    
                                    {{-- View Button --}}
                                    <a href="{{ route('dashboard.voters.show', $voter) }}" class="p-2 text-gray-500 hover:text-indigo-600 rounded-full hover:bg-gray-100 transition-colors" title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Edit Button --}}
                                    <a href="{{ route('dashboard.voters.edit', $voter) }}" class="p-2 text-gray-500 hover:text-blue-600 rounded-full hover:bg-gray-100 transition-colors" title="Edit Voter">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('dashboard.voters.destroy', $voter) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this voter?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 rounded-full hover:bg-red-50 transition-colors" title="Delete Voter">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No voters found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding a new voter.</p>
                            <div class="mt-6">
                                <a href="{{ route('dashboard.voters.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Voter
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
                
                {{-- Pagination Links (if you are paginating the results) --}}
                @if(method_exists($voters, 'links'))
                    <div class="mt-6">
                        {{ $voters->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>