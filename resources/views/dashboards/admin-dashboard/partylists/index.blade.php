<x-app-layout>
    <div class="max-w-5xl mx-auto py-8 px-4">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Partylist Management</h1>
                <p class="mt-1 text-[10px] text-slate-500 italic uppercase tracking-wider">Manage political parties for elections.</p>
            </div>
            <a href="{{ route('admin.partylists.create') }}" class="px-6 py-3 bg-sky-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-sky-100 hover:bg-sky-700 transition">
                + New Partylist
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white shadow-xl rounded-3xl border border-slate-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-6 py-4 text-[9px] font-black uppercase tracking-widest text-slate-400">Logo</th>
                        <th class="text-left px-6 py-4 text-[9px] font-black uppercase tracking-widest text-slate-400">Name</th>
                        <th class="text-left px-6 py-4 text-[9px] font-black uppercase tracking-widest text-slate-400">Description</th>
                        <th class="text-left px-6 py-4 text-[9px] font-black uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($partylists as $partylist)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                @if($partylist->logo_path)
                                    <img src="{{ Storage::url($partylist->logo_path) }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                                        <span class="text-sky-500 font-black text-xs">{{ substr($partylist->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-black text-slate-800 uppercase text-xs">{{ $partylist->name }}</p>
                                @if($partylist->alias)
                                    <p class="text-[9px] text-slate-400 uppercase">{{ $partylist->alias }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-[10px] text-slate-500 italic truncate max-w-xs">{{ $partylist->description ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.partylists.destroy', $partylist) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ $partylist->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[9px] font-black uppercase text-rose-500 hover:bg-rose-50 px-3 py-1.5 rounded-lg transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-xs italic">
                                No partylists yet. Create one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($partylists->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $partylists->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>