<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Election Management</h2>
                    <p class="text-sm text-slate-500 font-medium italic">Monitor and view ongoing or completed school elections.</p>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    {{-- Ginamit ang admin.elections.create base sa web.php --}}
                    <a href="{{ route('admin.elections.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        Create New
                    </a>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                <form method="GET" action="{{ route('admin.elections.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            class="block w-full pl-12 pr-4 py-4 border-slate-200 rounded-[1.5rem] text-sm font-bold focus:ring-indigo-500 shadow-sm border" 
                            placeholder="Search election title..." />
                    </div>
                    <button type="submit" class="px-8 py-4 bg-slate-100 text-slate-700 text-xs font-black uppercase tracking-widest rounded-[1.5rem] hover:bg-slate-200 transition">Filter Results</button>
                </form>
            </div>

            {{-- LIST OF ELECTIONS --}}
            <div class="space-y-6">
                @forelse ($elections as $election)
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">
                        <div class="p-8">
                            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                                
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        @php
                                            $now = now();
                                            $isCompleted = $election->end_at && $election->end_at < $now;
                                            $isActive = $election->is_active && $now->between($election->start_at, $election->end_at);
                                        @endphp
                                        
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                            {{ $isCompleted ? 'bg-slate-50 text-slate-400 border-slate-100' : 
                                               ($isActive ? 'bg-emerald-50 text-emerald-600 border-emerald-100 animate-pulse' : 'bg-amber-50 text-amber-600 border-amber-100') }}">
                                            {{ $isCompleted ? 'Completed' : ($isActive ? 'Active' : 'Upcoming/Inactive') }}
                                        </span>
                                        <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">{{ $election->title }}</h3>
                                    </div>
                                    
                                    <p class="text-sm text-slate-500 font-medium mb-4 leading-relaxed max-w-2xl">
                                        {{ $election->description ?? 'No description provided for this election.' }}
                                    </p>

                                    <div class="flex flex-wrap gap-x-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                            Started: {{ optional($election->start_at)->format('M d, Y h:i A') ?? 'TBD' }}
                                        </span>
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                                            Ends: {{ optional($election->end_at)->format('M d, Y h:i A') ?? 'TBD' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex gap-10 px-8 border-x border-slate-50 hidden xl:flex">
                                    <div class="text-center">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Positions</p>
                                        <p class="text-xl font-black text-slate-800">{{ $election->positions_count ?? 0 }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Votes</p>
                                        <p class="text-xl font-black text-slate-800">{{ $election->votes_count ?? 0 }}</p>
                                    </div>
                                </div>

                                <div>
                                    {{-- FIX: 'View Details' na lang ang natira, tinanggal ang Edit button --}}
                                    <a href="{{ route('admin.elections.show', $election->id) }}" class="inline-flex items-center justify-center px-10 py-4 bg-slate-50 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-transparent hover:border-indigo-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
                        <p class="text-slate-400 font-black uppercase text-xs tracking-widest italic">No elections available to display.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $elections->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>