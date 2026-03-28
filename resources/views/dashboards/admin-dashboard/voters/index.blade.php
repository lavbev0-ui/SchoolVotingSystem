<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Voter Management</h2>
                <p class="text-sm text-gray-500 italic">Manage student authorization and bulk registration.</p>
            </div>
            <div class="flex items-center gap-3 no-print">
                <a href="{{ route('admin.voters.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-black text-[10px] text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Single Voter
                </a>
            </div>
        </div>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs font-black uppercase tracking-widest">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl space-y-1">
                @foreach($errors->all() as $error)
                    <p class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        {{-- BULK IMPORT & SEARCH MODULES --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 no-print">

            {{-- BULK IMPORT MODULE --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-md">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 italic">Bulk Registration</h3>
                    <a href="{{ route('admin.voters.template') }}" class="text-[10px] text-indigo-600 font-black hover:underline uppercase tracking-tighter">
                        Download CSV Template
                    </a>
                </div>
                <form action="{{ route('admin.voters.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="relative">
                        <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" required
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border-2 border-dashed border-slate-100 rounded-2xl p-2">
                        <p class="text-[9px] text-slate-400 mt-2 font-bold uppercase tracking-widest pl-1">
                            Accepted: .csv, .xlsx, .xls — Max 10MB
                        </p>
                    </div>
                    <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200">
                        Upload & Sync Voters List
                    </button>
                </form>
            </div>

            {{-- SEARCH MODULE --}}
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 italic mb-6">Advanced Search</h3>
                <form method="GET" action="{{ route('admin.voters.index') }}" class="space-y-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="block w-full pl-11 pr-4 py-4 border-none bg-slate-50 rounded-2xl text-xs font-bold placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 transition-all"
                            placeholder="Find by name or Student ID..." />
                    </div>
                    <select name="grade_id"
                            class="block w-full px-4 py-4 border-none bg-slate-50 rounded-2xl text-xs font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="">All Grade Levels</option>
                        @foreach($gradeLevels as $grade)
                            <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="section_id"
                            class="block w-full px-4 py-4 border-none bg-slate-50 rounded-2xl text-xs font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-4 bg-indigo-50 text-indigo-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-all">
                            Filter Results
                        </button>
                        @if(request('search') || request('grade_id') || request('section_id'))
                            <a href="{{ route('admin.voters.index') }}" class="px-6 py-4 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Main Content Card --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2.5rem] border border-gray-100">

            <div class="px-6 py-5 border-b border-gray-50 bg-gray-50/30">
                <div class="mb-4">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Authorized Voter Registry</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">
                        @if(request('search') || request('grade_id') || request('section_id'))
                            Showing filtered results — {{ $voters->total() }} {{ Str::plural('record', $voters->total()) }} found
                        @else
                            Verified list of eligible student voters — sorted alphabetically.
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-widest whitespace-nowrap">
                        Total: {{ $voters->total() }}
                    </span>
                    <button onclick="document.getElementById('voterListModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-sm whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Voter List
                    </button>
                    <a href="{{ route('admin.voters.export') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-sm whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        Download
                    </a>
                </div>
            </div>

            <div class="p-8">
                <div class="space-y-3">
                    @forelse($voters as $voter)
                        @php
                            $colors = ['bg-indigo-500','bg-violet-500','bg-sky-500','bg-emerald-500','bg-amber-500','bg-rose-500','bg-pink-500','bg-teal-500'];
                            $colorClass = $colors[crc32($voter->first_name) % count($colors)];
                            $initials = strtoupper(substr($voter->first_name, 0, 1) . substr($voter->last_name, 0, 1));
                        @endphp
                        <div class="flex flex-col md:flex-row md:items-center justify-between p-5 border border-slate-50 rounded-[1.5rem] hover:border-indigo-200 hover:bg-indigo-50/20 transition-all gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-5">

                                    <div class="w-14 h-14 flex-shrink-0">
                                        @if($voter->photo_path)
                                            <img src="{{ asset('storage/' . $voter->photo_path) }}"
                                                 alt="{{ $voter->first_name }}"
                                                 class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-14 h-14 rounded-2xl bg-slate-100 hidden items-center justify-center border-2 border-white shadow-sm" style="display:none;">
                                                <span class="text-indigo-500 font-black text-xl">{{ strtoupper(substr($voter->first_name, 0, 1)) }}</span>
                                            </div>
                                        @else
                                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center border-2 border-white shadow-sm">
                                                <span class="text-indigo-500 font-black text-xl">{{ strtoupper(substr($voter->first_name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-black text-slate-900 text-base uppercase tracking-tight leading-none">{{ $voter->full_name }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-white text-slate-500 border border-slate-200 shadow-sm">
                                                ID: {{ $voter->student_id ?? $voter->userID }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100">
                                                {{ $voter->gradeLevel->name ?? 'No Grade' }}
                                            </span>
                                            @if($voter->section)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                    SEC: {{ $voter->section->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-4 md:pt-0 border-t md:border-none border-gray-50">
                                <a href="{{ route('admin.voters.show', $voter->id) }}" class="p-3 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-xl transition-all border border-transparent hover:border-indigo-100 shadow-sm" title="Profile">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.voters.edit', $voter->id) }}" class="p-3 text-slate-400 hover:text-amber-600 hover:bg-white rounded-xl transition-all border border-transparent hover:border-amber-100 shadow-sm" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.voters.reset-password', $voter->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Reset password of {{ addslashes($voter->full_name) }} to their Student ID?');">
                                    @csrf
                                    <button type="submit" class="p-3 text-slate-400 hover:text-blue-600 hover:bg-white rounded-xl transition-all border border-transparent hover:border-blue-100 shadow-sm" title="Reset Password">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                            @if(request('search') || request('grade_id') || request('section_id'))
                                <p class="text-slate-400 font-black uppercase text-xs tracking-widest">No voters found matching your filters.</p>
                                <a href="{{ route('admin.voters.index') }}" class="mt-4 inline-block text-[10px] text-indigo-600 font-black uppercase tracking-widest hover:underline">
                                    Clear Filters
                                </a>
                            @else
                                <p class="text-slate-400 font-black uppercase text-xs tracking-widest">No matching voters in registry.</p>
                            @endif
                        </div>
                    @endforelse
                </div>

                <div class="mt-10">
                    {{ $voters->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- VOTER LIST MODAL --}}
    <div id="voterListModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-8 py-5 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-widest text-slate-900">Voter List</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Complete voter information registry</p>
                </div>
                <button onclick="document.getElementById('voterListModal').classList.add('hidden')"
                    class="p-2 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="overflow-auto flex-1 px-8 py-6">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            <th class="pb-3 text-left">Photo</th>
                            <th class="pb-3 text-left">Student ID</th>
                            <th class="pb-3 text-left">Full Name</th>
                            <th class="pb-3 text-left">Email</th>
                            <th class="pb-3 text-left">Grade Level</th>
                            <th class="pb-3 text-left">Section</th>
                            <th class="pb-3 text-left">Status</th>
                            <th class="pb-3 text-left">Pwd Changed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($voters as $voter)
                        @php $initial = strtoupper(substr($voter->first_name, 0, 1)); @endphp
                        <tr class="hover:bg-slate-50 transition-all">
                            <td class="py-3">
                                @if($voter->photo_path)
                                    <img src="{{ asset('storage/' . $voter->photo_path) }}"
                                         alt="{{ $voter->first_name }}"
                                         class="w-9 h-9 rounded-xl object-cover border border-slate-100"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 hidden items-center justify-center" style="display:none;">
                                        <span class="text-indigo-500 font-black text-xs">{{ $initial }}</span>
                                    </div>
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center">
                                        <span class="text-indigo-500 font-black text-xs">{{ $initial }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 font-black text-slate-700">{{ $voter->student_id ?? $voter->userID }}</td>
                            <td class="py-3 font-bold text-slate-900">{{ $voter->full_name }}</td>
                            <td class="py-3 text-slate-500">{{ $voter->email ?? '—' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase">
                                    {{ $voter->gradeLevel->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">
                                    {{ $voter->section->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($voter->is_active)
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">Active</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-rose-50 text-rose-500 border border-rose-100 uppercase">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($voter->password_changed)
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase">Yes</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-[9px] font-black bg-slate-50 text-slate-400 border border-slate-100 uppercase">No</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-4 border-t border-slate-100 flex justify-between items-center">
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">
                    Showing {{ $voters->count() }} of {{ $voters->total() }} voters
                </p>
                <button onclick="document.getElementById('voterListModal').classList.add('hidden')"
                    class="px-6 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-700 transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>

</x-app-layout>