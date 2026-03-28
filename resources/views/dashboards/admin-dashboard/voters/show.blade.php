<x-app-layout>
    <div class="p-6 bg-slate-50 min-h-screen">
        
        {{-- TOP NAVIGATION --}}
        <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between">
            {{-- FIX: Iniba ang route name mula dashboard.voters.index patungong admin.voters.index --}}
            <a href="{{ route('admin.voters.index') }}" class="group flex items-center gap-2 text-slate-500 hover:text-blue-600 transition-colors">
                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm group-hover:bg-blue-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </div>
                <span class="text-sm font-bold tracking-tight">Back to Registry</span>
            </a>
            
            <div class="flex gap-2">
                {{-- FIX: Iniba ang route name patungong admin.voters.edit --}}
                <a href="{{ route('admin.voters.edit', $voter->id) }}" class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                    Edit Profile
                </a>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                
                {{-- HEADER/BANNER --}}
                @php
                    $isActive = $voter->is_active ?? true; 
                @endphp
                <div class="relative h-32 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-600">
                    <div class="absolute -bottom-12 left-8 flex items-end gap-6">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-3xl bg-white p-1.5 shadow-xl">
                                @if($voter->photo_path)
                                    <img src="{{ asset('storage/' . $voter->photo_path) }}" class="w-full h-full object-cover rounded-[1.2rem]">
                                @else
                                    {{-- FIX: Ginagamit ang photo_url accessor na ginawa natin sa Model --}}
                                    <img src="{{ $voter->photo_url }}" class="w-full h-full object-cover rounded-[1.2rem]">
                                @endif
                            </div>
                            <div class="absolute bottom-2 -right-2 w-8 h-8 {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }} border-4 border-white rounded-full shadow-sm"></div>
                        </div>
                        <div class="mb-2">
                            <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                                {{ $voter->full_name }}
                            </h1>
                            {{-- Ginagamit ang student_id gaya ng nasa database schema --}}
                            <p class="text-slate-500 font-bold text-sm">STUDENT ID: <span class="text-blue-600 font-mono">{{ $voter->student_id }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="pt-20 px-8 pb-8">
                    {{-- STATS STRIP --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                            <p class="text-sm font-bold {{ $isActive ? 'text-emerald-600' : 'text-slate-500' }} mt-1">
                                {{ $isActive ? 'Verified Active' : 'Account Disabled' }}
                            </p>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Grade & Section</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">
                                {{ $voter->gradeLevel->name ?? 'N/A' }} - {{ $voter->section->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Registered On</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">
                                {{ $voter->created_at ? $voter->created_at->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        {{-- DETAILS --}}
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                Personal Information
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Full Legal Name</label>
                                    <p class="text-slate-700 font-bold border-b border-slate-50 pb-2 mt-1 uppercase">
                                        {{ $voter->full_name }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Email Address</label>
                                    <p class="text-slate-700 font-bold border-b border-slate-50 pb-2 mt-1">{{ $voter->email ?? 'No email provided' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- VOTING HISTORY --}}
                        <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                                <span class="w-2 h-2 bg-indigo-600 rounded-full"></span>
                                Participation History
                            </h3>
                            
                            @php
                                // Kinukuha ang unique elections kung saan bumoto ang voter
                                $history = $voter->votes ? $voter->votes->unique('election_id') : collect();
                            @endphp

                            @if($history->count() > 0)
                                <div class="space-y-3">
                                    @foreach($history as $vote)
                                        <div class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-2xl shadow-sm">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <span class="text-xs font-bold text-slate-700 truncate max-w-[150px]">
                                                    {{ $vote->election->title ?? 'Untitled Election' }}
                                                </span>
                                            </div>
                                            <span class="text-[9px] font-black text-slate-400 uppercase">
                                                {{ $vote->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-slate-50 rounded-[2rem] p-10 text-center border-2 border-dashed border-slate-200">
                                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm mx-auto mb-4">
                                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">No Votes Recorded Yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- FOOTER / INFO --}}
                <div class="bg-slate-50 border-t border-slate-100 px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        Record updated: {{ $voter->updated_at->diffForHumans() }}
                    </p>
                    {{-- FIX: Iniba ang destroy route name patungong admin.voters.destroy --}}
                    <form action="{{ route('admin.voters.destroy', $voter->id) }}" method="POST" onsubmit="return confirm('WARNING: This will permanently delete this voter record. Proceed?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-[0.2em] transition-colors">
                            Purge Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>