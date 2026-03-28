<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-10">
        {{-- ELECTION HISTORY LIST SECTION --}}
        <div class="bg-white overflow-hidden shadow-xl rounded-[2.5rem] border border-slate-100">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Election History</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Review past tally and official results</p>
                </div>
                <div class="h-12 w-12 bg-sky-50 rounded-2xl flex items-center justify-center text-sky-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                </div>
            </div>

            <div class="p-8 space-y-4">
                @forelse ($completedElections as $hist)
                    <div class="flex items-center justify-between p-6 border border-slate-100 rounded-[2rem] hover:bg-slate-50 transition-all group">
                        <div class="flex-1">
                            <h4 class="text-lg font-black text-slate-800 uppercase tracking-tight">{{ $hist->title }}</h4>
                            <div class="flex items-center gap-4 mt-2">
                                <span class="text-[9px] font-black uppercase px-3 py-1 rounded-full {{ $hist->status === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                                    {{ $hist->status }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"></path></svg>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Ended: {{ $hist->end_at->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 ml-2">
                                    <div class="h-1.5 w-1.5 rounded-full bg-sky-400"></div>
                                    <p class="text-[10px] font-black text-sky-500 uppercase">Turnout: {{ number_format($hist->unique_votes_count) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- [UPDATE] PINALITAN ANG ROUTE NAME PARA SA BAGONG TALLY VIEW --}}
                        <a href="{{ route('admin.results.show', ['election' => $hist->id]) }}" 
                           class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-sky-600 shadow-xl transition-all active:scale-95 group">
                            Review Tally
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-20 bg-slate-50/50 rounded-[2rem] border-2 border-dashed border-slate-100">
                        <p class="text-slate-400 uppercase font-black text-xs tracking-widest">No completed elections found in history.</p>
                    </div>
                @endforelse
            </div>

            <div class="px-8 pb-8">
                {{ $completedElections->links() }}
            </div>
        </div>
    </div>
</x-app-layout>