<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Voter Profile') }}
            </h2>
            <a href="{{ route('dashboard.voters.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center gap-1">
                &larr; Back to List
            </a>
        </div>
    </x-slot>

    {{-- Setup Status Logic (Mimicking the React getStatusConfig) --}}
    @php
        $isActive = $voter->is_active;
        
        $statusStyles = $isActive 
            ? [
                'bg' => 'bg-gradient-to-br from-green-50 to-emerald-50',
                'border' => 'border-green-200',
                'text' => 'text-green-700',
                'icon_color' => 'text-green-600',
                'label' => 'Active Account'
            ]
            : [
                'bg' => 'bg-gradient-to-br from-yellow-50 to-amber-50',
                'border' => 'border-yellow-200',
                'text' => 'text-yellow-700',
                'icon_color' => 'text-yellow-600',
                'label' => 'Pending Activation'
            ];
    @endphp

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Main Container (replaces DialogContent) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                
                {{-- Header Section --}}
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-2xl font-semibold tracking-tight">Voter Profile</h3>
                    <p class="text-sm text-gray-500">Complete information and voting history</p>
                </div>

                <div class="p-6 space-y-6">
                    
                    {{-- 1. Profile Banner Section --}}
                    <div class="{{ $statusStyles['bg'] }} {{ $statusStyles['border'] }} border-2 rounded-xl p-6">
                        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
                            
                            {{-- Avatar --}}
                            <div class="relative">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg overflow-hidden">
                                    @if($voter->photo_path)
                                        <img src="{{ asset('storage/' . $voter->photo_path) }}" alt="Photo" class="w-full h-full object-cover">
                                    @else
                                        {{-- User Icon --}}
                                        <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    @endif
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 {{ $statusStyles['bg'] }} rounded-full flex items-center justify-center border-2 {{ $statusStyles['border'] }}">
                                    @if($isActive)
                                        <svg class="w-4 h-4 {{ $statusStyles['icon_color'] }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                                    @else
                                        <svg class="w-4 h-4 {{ $statusStyles['icon_color'] }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                    {{ $voter->first_name }} {{ $voter->middle_name ? $voter->middle_name[0].'.' : '' }} {{ $voter->last_name }} {{ $voter->suffix }}
                                </h3>
                                
                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-3">
                                    {{-- ID Badge --}}
                                    <span class="inline-flex items-center rounded-md border border-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-700 font-mono">
                                        ID: {{ $voter->userID }}
                                    </span>
                                    {{-- Status Badge --}}
                                    <span class="inline-flex items-center rounded-md border {{ $statusStyles['border'] }} {{ $statusStyles['bg'] }} px-2.5 py-0.5 text-xs font-semibold {{ $statusStyles['text'] }}">
                                        @if($isActive)
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        @endif
                                        {{ $statusStyles['label'] }}
                                    </span>
                                </div>

                                {{-- Quick Stats --}}
                                <div class="flex justify-center sm:justify-start gap-6 mt-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm">
                                            {{-- Vote Icon --}}
                                            <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"/><path d="M22 19H2"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Elections Voted</p>
                                            <p class="text-lg font-bold text-gray-900">
                                                {{-- Placeholder for relationship count --}}
                                                {{ $voter->participatedElections ? count($voter->participatedElections) : 0 }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 bg-white/80 rounded-lg flex items-center justify-center shadow-sm">
                                            {{-- Calendar Icon --}}
                                            <svg class="w-5 h-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Member Since</p>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $voter->created_at->format('M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Separator --}}
                    <div class="h-px w-full bg-gray-200"></div>

                    {{-- 2. Personal Information Grid --}}
                    <div>
                        <h4 class="font-semibold text-lg mb-4 flex items-center gap-2 text-gray-900">
                            {{-- User Icon Small --}}
                            <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Personal Information
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Email --}}
                            <div class="bg-white rounded-lg border-2 border-gray-100 p-6 hover:border-blue-200 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Email Address</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $voter->email }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Registration Date --}}
                            <div class="bg-white rounded-lg border-2 border-gray-100 p-6 hover:border-purple-200 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Registration Date</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $voter->created_at->format('F d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Separator --}}
                    <div class="h-px w-full bg-gray-200"></div>

                    {{-- 3. Academic Information --}}
                    <div>
                        <h4 class="font-semibold text-lg mb-4 flex items-center gap-2 text-gray-900">
                            {{-- Graduation Cap Icon --}}
                            <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            Academic Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Grade Level --}}
                            <div class="bg-white rounded-lg border-2 border-gray-100 p-6 hover:border-orange-200 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        {{-- Award Icon --}}
                                        <svg class="w-5 h-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Grade Level</p>
                                        <p class="text-lg font-bold text-orange-600">{{ $voter->gradeLevel->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section --}}
                            <div class="bg-white rounded-lg border-2 border-gray-100 p-6 hover:border-indigo-200 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                        {{-- Users Icon --}}
                                        <svg class="w-5 h-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Section</p>
                                        <p class="text-lg font-bold text-indigo-600">{{ $voter->section->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Separator --}}
                    <div class="h-px w-full bg-gray-200"></div>

                    {{-- 4. Voting History (Conditional) --}}
                    <div>
                        <h4 class="font-semibold text-lg mb-4 flex items-center gap-2 text-gray-900">
                            {{-- Vote Icon --}}
                            <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"/><path d="M22 19H2"/></svg>
                            Voting History
                        </h4>

                        @if(false) {{-- Replace 'false' with '$voter->participatedElections && $voter->participatedElections->count() > 0' when you have the relationship --}}
                             <div class="space-y-3">
                                {{-- Loop through elections here --}}
                                {{-- 
                                @foreach($voter->participatedElections as $election)
                                    <div class="bg-white rounded-lg border-2 border-gray-100 p-4 hover:shadow-md transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-500 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                                                <svg class="w-6 h-6 text-white" ...check icon...></svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $election->title }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Vote successfully recorded</p>
                                            </div>
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Completed</span>
                                        </div>
                                    </div>
                                @endforeach
                                --}}
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="border-2 border-dashed border-gray-200 rounded-lg p-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"/><path d="M22 19H2"/></svg>
                                </div>
                                <p class="text-gray-500 font-medium">No voting history yet</p>
                                <p class="text-sm text-gray-400 mt-1">This voter hasn't participated in any elections</p>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Footer / Actions --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    
                    {{-- Delete --}}
                    <form action="{{ route('dashboard.voters.destroy', $voter->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background hover:bg-red-100 hover:text-red-700 h-10 py-2 px-4 text-red-600">
                            Delete
                        </button>
                    </form>

                    {{-- Edit --}}
                    <a href="{{ route('dashboard.voters.edit', $voter->id) }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none ring-offset-background bg-gray-900 text-white hover:bg-gray-900/90 h-10 py-2 px-4">
                        Edit Details
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>