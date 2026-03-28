<x-app-layout>
    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- HEADER SECTION --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">System Settings</h2>
                    <p class="text-sm text-slate-500 font-medium italic">Configure system preferences, security, and global voting rules.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit" form="settings-form"
                            class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-indigo-700 transition shadow-xl shadow-indigo-100">
                        Save Preferences
                    </button>
                </div>
            </div>

            {{-- SUCCESS ALERT --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl font-bold text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </span>
                </div>
            @endif

            {{-- FORM START --}}
            <form id="settings-form" method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    {{-- ELECTION RULES --}}
                    <div class="bg-white shadow-sm border border-slate-100 rounded-[2.5rem] p-8 space-y-8">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-1.5 h-5 bg-indigo-600 rounded-full"></div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Election Rules</h3>
                        </div>

                        {{-- Allow Vote Changes --}}
                        <div class="flex justify-between items-center p-4 rounded-3xl bg-slate-50/50 border border-slate-100 hover:bg-white transition-colors">
                            <div class="max-w-[70%]">
                                <p class="font-black text-slate-800 uppercase text-[11px] tracking-tight mb-1">Allow Vote Changes</p>
                                <p class="text-[10px] text-slate-400 font-medium leading-tight">Allows voters to modify their ballots before the election deadline.</p>
                            </div>
                            <div class="relative inline-block w-12 h-6">
                                <input type="hidden" name="allow_vote_changes" value="0">
                                <input type="checkbox" name="allow_vote_changes" value="1"
                                       @checked(($settings['allow_vote_changes'] ?? '0') == '1')
                                       class="peer appearance-none w-12 h-6 rounded-full bg-slate-200 checked:bg-indigo-600 cursor-pointer transition-colors duration-200">
                                <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 peer-checked:translate-x-6 pointer-events-none"></span>
                            </div>
                        </div>

                        {{-- Real-time Results --}}
                        <div class="flex justify-between items-center p-4 rounded-3xl bg-slate-50/50 border border-slate-100 hover:bg-white transition-colors">
                            <div class="max-w-[70%]">
                                <p class="font-black text-slate-800 uppercase text-[11px] tracking-tight mb-1">Real-time Results</p>
                                <p class="text-[10px] text-slate-400 font-medium leading-tight">Displays live vote tallies on the voter's dashboard during the election.</p>
                            </div>
                            <div class="relative inline-block w-12 h-6">
                                <input type="hidden" name="real_time_results" value="0">
                                <input type="checkbox" name="real_time_results" value="1"
                                       @checked(($settings['real_time_results'] ?? '0') == '1')
                                       class="peer appearance-none w-12 h-6 rounded-full bg-slate-200 checked:bg-indigo-600 cursor-pointer transition-colors duration-200">
                                <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 peer-checked:translate-x-6 pointer-events-none"></span>
                            </div>
                        </div>
                    </div>

                    {{-- SECURITY & ACCESS --}}
                    <div class="bg-white shadow-sm border border-slate-100 rounded-[2.5rem] p-8 space-y-8">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-1.5 h-5 bg-rose-500 rounded-full"></div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Security & Access</h3>
                        </div>

                        {{-- Require Voter 2FA --}}
                        <div class="flex justify-between items-center p-4 rounded-3xl bg-slate-50/50 border border-slate-100 hover:bg-white transition-colors">
                            <div class="max-w-[70%]">
                                <p class="font-black text-slate-800 uppercase text-[11px] tracking-tight mb-1">Require Voter 2FA</p>
                                <p class="text-[10px] text-slate-400 font-medium leading-tight">Enforces two-factor authentication for all student accounts via email or SMS.</p>
                            </div>
                            <div class="relative inline-block w-12 h-6">
                                <input type="hidden" name="require_2fa" value="0">
                                <input type="checkbox" name="require_2fa" value="1"
                                       @checked(($settings['require_2fa'] ?? '0') == '1')
                                       class="peer appearance-none w-12 h-6 rounded-full bg-slate-200 checked:bg-rose-500 cursor-pointer transition-colors duration-200">
                                <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 peer-checked:translate-x-6 pointer-events-none"></span>
                            </div>
                        </div>

                        {{-- Require Admin 2FA --}}
                        <div class="flex justify-between items-center p-4 rounded-3xl bg-rose-50/30 border border-rose-100 hover:bg-white transition-colors">
                            <div class="max-w-[70%]">
                                <p class="font-black text-slate-800 uppercase text-[11px] tracking-tight mb-1">Require Admin 2FA</p>
                                <p class="text-[10px] text-slate-400 font-medium leading-tight">Enforces two-factor authentication for admin accounts via email OTP.</p>
                            </div>
                            <div class="relative inline-block w-12 h-6">
                                <input type="hidden" name="require_admin_2fa" value="0">
                                <input type="checkbox" name="require_admin_2fa" value="1"
                                       @checked(($settings['require_admin_2fa'] ?? '0') == '1')
                                       class="peer appearance-none w-12 h-6 rounded-full bg-slate-200 checked:bg-rose-500 cursor-pointer transition-colors duration-200">
                                <span class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 peer-checked:translate-x-6 pointer-events-none"></span>
                            </div>
                        </div>

                        {{-- Voter Session Timeout --}}
                        <div class="space-y-3">
                            <label class="font-black text-slate-800 uppercase text-[11px] tracking-tight ml-1">Voter Session Timeout</label>
                            <div class="relative">
                                <select name="session_timeout"
                                        class="block w-full pl-4 pr-10 py-4 text-sm border-slate-200 focus:ring-indigo-500 focus:border-indigo-500 rounded-2xl font-bold text-slate-600 bg-slate-50/50 border">
                                    <option value="15"  @selected(($settings['session_timeout'] ?? '') == '15')>15 Minutes</option>
                                    <option value="30"  @selected(($settings['session_timeout'] ?? '') == '30')>30 Minutes</option>
                                    <option value="60"  @selected(($settings['session_timeout'] ?? '') == '60')>1 Hour</option>
                                    <option value="120" @selected(($settings['session_timeout'] ?? '') == '120')>2 Hours</option>
                                </select>
                            </div>
                            <p class="text-[9px] text-slate-400 font-medium ml-1 italic">Automatic logout after inactivity period for voters.</p>
                        </div>
                    </div>

                </div>
            </form>

            {{-- DATABASE BACKUP SECTION --}}
            <div class="bg-white shadow-sm border border-slate-100 rounded-[2.5rem] p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Database Backup</h3>
                </div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 rounded-3xl bg-emerald-50/50 border border-emerald-100">
                    <div>
                        <p class="font-black text-slate-800 uppercase text-[11px] tracking-tight mb-1">Download Full Backup</p>
                        <p class="text-[10px] text-slate-400 font-medium leading-tight">
                            Generates a complete SQL backup of the database including all voters, elections, votes, and logs.
                            The file can be used to restore the system on any MySQL server.
                        </p>
                    </div>
                    <a href="{{ route('admin.backup.download') }}"
                       class="flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest transition shadow-lg shadow-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        Download .SQL Backup
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>