<x-app-layout>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div x-data="dashboardPolling()" x-init="startPolling()" class="p-6 bg-slate-50 min-h-screen">

        {{-- HEADER --}}
        <div class="mb-8 border-b border-slate-200 pb-4 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Admin Control Center</h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Real-time system overview and voter analytics.</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Server Time</span>
                <p class="text-xs font-bold text-slate-700 uppercase" x-text="serverTime">{{ now()->format('M d, Y | h:i A') }}</p>
            </div>
        </div>

        {{-- TOP STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-700 transition-all hover:shadow-md">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Total Registered</p>
                <h3 class="text-3xl font-black text-slate-800 mt-1" x-text="stats.totalRegistered">0</h3>
                <p class="text-[10px] text-slate-400 mt-1 italic font-medium uppercase">Students in database</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-sky-400 transition-all hover:shadow-md">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Total Ballots Cast</p>
                <h3 class="text-3xl font-black text-slate-800 mt-1" x-text="stats.votesRecorded">0</h3>
                <div class="mt-2 space-y-0.5">
                    <template x-for="election in stats.activeElections" :key="election.id">
                        <p class="text-[8px] font-bold text-slate-400 uppercase truncate"
                           x-text="election.title + ': ' + election.unique_voters_count + ' voters'"></p>
                    </template>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-indigo-500 transition-all hover:shadow-md">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Avg. Turnout Rate</p>
                <h3 class="text-3xl font-black text-slate-800 mt-1" x-text="stats.turnoutPercentage.toFixed(1) + '%'">0.0%</h3>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden shadow-inner">
                    <div class="bg-indigo-600 h-full transition-all duration-1000" :style="'width: ' + Math.min(stats.turnoutPercentage, 100) + '%'"></div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-slate-600 transition-all hover:shadow-md">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Live Polls</p>
                <h3 class="text-3xl font-black text-slate-800 mt-1" x-text="stats.activeElectionsCount">0</h3>
                <p class="text-[10px] text-slate-500 mt-2 font-black flex items-center gap-1 uppercase tracking-tighter">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                    </span>
                    Monitoring Activity
                </p>
            </div>
        </div>

        {{-- VISUAL ANALYTICS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col items-center">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 self-start">Avg. Participation Ratio</h3>
                <div class="relative h-48 w-48">
                    <canvas id="turnoutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <p class="text-2xl font-black text-slate-900 leading-none" x-text="Math.round(stats.turnoutPercentage) + '%'"></p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">Avg. Voted</p>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Turnout by Grade Level</h3>
                <div class="h-48">
                    <canvas id="gradeBarChart"></canvas>
                </div>
            </div>
        </div>

        {{-- LIVE TALLYING --}}
        <template x-if="stats.activeElections && stats.activeElections.length > 0">
            <div class="mb-8 space-y-8">
                <template x-for="election in stats.activeElections" :key="election.id">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-8 border-b pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-6 bg-indigo-600 rounded-full"></div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight" x-text="election.title + ' Live Tallying'"></h3>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5"
                                       x-text="election.unique_voters_count + ' of ' + election.total_registered + ' voters (' + election.turnout_percentage + '% turnout)'"></p>
                                </div>
                            </div>
                            <span class="text-[9px] bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full font-black uppercase tracking-widest border border-emerald-100 animate-pulse">Live Processing</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <template x-for="position in election.positions" :key="position.id">
                                <div class="bg-slate-50/50 p-6 rounded-3xl border border-slate-100 space-y-5">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]" x-text="position.title"></p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-0.5" x-text="'Total Ballots: ' + getTotalVotes(position)"></p>
                                    </div>
                                    <div class="h-48 w-full">
                                        <canvas :id="'chart-' + position.id"></canvas>
                                    </div>
                                    <div class="space-y-2">
                                        <template x-for="(candidate, index) in sortedCandidates(position)" :key="candidate.id">
                                            <div class="relative p-3 rounded-2xl border overflow-hidden transition-all"
                                                 :class="isLeading(candidate, position) ? 'border-indigo-200 bg-indigo-50/50' : 'border-slate-100 bg-white'">
                                                <div class="absolute inset-y-0 left-0 bg-indigo-500/5 transition-all duration-700"
                                                     :style="'width: ' + getPercentage(candidate, position) + '%'"></div>
                                                <div class="relative z-10 flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <template x-if="isLeading(candidate, position) && !isTie(position)">
                                                            <span class="text-[7px] font-black bg-emerald-100 text-emerald-600 px-1.5 py-0.5 rounded-full border border-emerald-200 uppercase shrink-0">Leading</span>
                                                        </template>
                                                        <template x-if="isLeading(candidate, position) && isTie(position)">
                                                            <span class="text-[7px] font-black bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded-full border border-amber-200 uppercase shrink-0">Tie</span>
                                                        </template>
                                                        <template x-if="!isLeading(candidate, position)">
                                                            <div class="w-5 h-5 bg-slate-200 text-slate-500 text-[9px] font-black rounded-full flex items-center justify-center shrink-0" x-text="index + 1"></div>
                                                        </template>
                                                        <div class="min-w-0">
                                                            <p class="text-[10px] font-black text-slate-900 uppercase truncate" x-text="candidate.first_name + ' ' + candidate.last_name"></p>
                                                            <p class="text-[8px] font-bold text-indigo-400 uppercase truncate" x-text="candidate.party || 'Independent'"></p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right shrink-0">
                                                        <p class="text-base font-black text-slate-900 leading-none" x-text="candidate.votes_count"></p>
                                                        <p class="text-[8px] font-bold text-slate-400 uppercase" x-text="getPercentage(candidate, position).toFixed(1) + '%'"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        {{-- VOTER ACTIVITY LOG — Full width, no Live Feed --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Voter Activity Log
                </h3>
                <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100 uppercase"
                      x-text="stats.votedVotersList ? stats.votedVotersList.length + ' Records' : '0 Records'"></span>
            </div>

            <div x-data="{ search: '', searchGrade: '', selectedAction: 'all' }">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
                    <input type="text" x-model="search" placeholder="Search voter name..."
                           class="px-4 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    <input type="text" x-model="searchGrade" placeholder="Search grade & section..."
                           class="px-4 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    <select x-model="selectedAction"
                            class="px-3 py-2 text-[10px] font-black border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white text-slate-600 uppercase">
                        <option value="all">All Activities</option>
                        <option value="voted">Voted</option>
                        <option value="password_changed">Password Changed</option>
                    </select>
                </div>

                <div class="overflow-auto max-h-[400px] custom-scrollbar">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-white">
                            <tr class="border-b border-slate-100">
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">#</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Voter</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Grade & Section</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Activity</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="stats.votedVotersList && stats.votedVotersList.length > 0">
                                <template
                                    x-for="(voter, index) in stats.votedVotersList.filter(v =>
                                        v.voter_name.toLowerCase().includes(search.toLowerCase()) &&
                                        (v.grade + ' ' + v.section).toLowerCase().includes(searchGrade.toLowerCase()) &&
                                        (selectedAction === 'all' || v.action === selectedAction)
                                    )"
                                    :key="index">
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                                        <td class="py-3 pr-4"><span class="text-[9px] font-black text-slate-400" x-text="index + 1"></span></td>
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                                    <span class="text-[9px] font-black text-indigo-600" x-text="voter.voter_name.charAt(0)"></span>
                                                </div>
                                                <span class="text-[10px] font-black text-slate-800 uppercase" x-text="voter.voter_name"></span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4"><span class="text-[9px] font-bold text-slate-500" x-text="voter.grade + ' — ' + voter.section"></span></td>
                                        <td class="py-3 pr-4">
                                            <span class="text-[9px] font-black px-2 py-0.5 rounded-full border uppercase"
                                                  :class="{
                                                      'bg-emerald-50 text-emerald-600 border-emerald-100': voter.action === 'voted',
                                                      'bg-amber-50 text-amber-600 border-amber-100': voter.action === 'password_changed',
                                                      'bg-slate-50 text-slate-500 border-slate-100': voter.action !== 'voted' && voter.action !== 'password_changed'
                                                  }"
                                                  x-text="voter.action === 'voted' ? 'Voted' : voter.action === 'password_changed' ? 'Password Changed' : voter.action">
                                            </span>
                                            <p class="text-[8px] text-slate-400 mt-0.5 truncate max-w-[160px]" x-text="voter.description"></p>
                                        </td>
                                        <td class="py-3"><span class="text-[9px] font-bold text-slate-400" x-text="voter.voted_at"></span></td>
                                    </tr>
                                </template>
                            </template>
                            <template x-if="!stats.votedVotersList || stats.votedVotersList.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">No activity recorded yet</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ADMIN AUDIT TRAIL — No IP Address --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6 border-b pb-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Admin Audit Trail
                </h3>
                <span class="text-[9px] font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-full border border-rose-100 uppercase">
                    {{ isset($adminActivityLogs) ? count($adminActivityLogs) : 0 }} Records
                </span>
            </div>

            {{-- ✅ Alpine-driven filtering — no more x-show on Blade rows --}}
            <div x-data="{
                adminSearch: '',
                adminAction: 'all',
                allLogs: {{ Js::from($adminActivityLogs ?? []) }},
                get filteredLogs() {
                    return this.allLogs.filter(log =>
                        (this.adminAction === 'all' || log.action === this.adminAction) &&
                        (log.admin_name.toLowerCase().includes(this.adminSearch.toLowerCase()) ||
                         log.description.toLowerCase().includes(this.adminSearch.toLowerCase()))
                    );
                },
                getActionClass(action) {
                    if (action === 'admin_login') return 'bg-blue-50 text-blue-600 border-blue-100';
                    if (action === 'viewed_dashboard') return 'bg-slate-50 text-slate-400 border-slate-100';
                    if (action.includes('created')) return 'bg-emerald-50 text-emerald-600 border-emerald-100';
                    if (action.includes('deleted')) return 'bg-rose-50 text-rose-600 border-rose-100';
                    if (action.includes('updated')) return 'bg-amber-50 text-amber-600 border-amber-100';
                    if (action.includes('reset')) return 'bg-purple-50 text-purple-600 border-purple-100';
                    if (action.includes('imported')) return 'bg-sky-50 text-sky-600 border-sky-100';
                    return 'bg-slate-50 text-slate-500 border-slate-100';
                }
            }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-4">
                    <input type="text" x-model="adminSearch" placeholder="Search admin name or description..."
                           class="px-4 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-300 font-medium">
                    <select x-model="adminAction"
                            class="px-3 py-2 text-[10px] font-black border border-slate-200 rounded-xl focus:outline-none bg-white text-slate-600 uppercase">
                        <option value="all">All Actions</option>
                        <option value="admin_login">Login</option>
                        <option value="viewed_dashboard">Viewed Dashboard</option>
                        <option value="created_election">Created Election</option>
                        <option value="created_voter">Created Voter</option>
                        <option value="updated_voter">Updated Voter</option>
                        <option value="imported_voters">Imported Voters</option>
                        <option value="reset_voter_password">Reset Password</option>
                        <option value="updated_setting">Updated Setting</option>
                    </select>
                </div>

                <div class="overflow-auto max-h-[400px] custom-scrollbar">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-white">
                            <tr class="border-b border-slate-100">
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">#</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Admin</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Action</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3 pr-4">Description</th>
                                <th class="text-[9px] font-black text-slate-400 uppercase tracking-widest pb-3">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- ✅ Alpine x-for — only renders matched rows, no hidden rows --}}
                            <template x-if="filteredLogs.length > 0">
                                <template x-for="(log, index) in filteredLogs" :key="index">
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                                        <td class="py-3 pr-4"><span class="text-[9px] font-black text-slate-400" x-text="index + 1"></span></td>
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center shrink-0">
                                                    <span class="text-[9px] font-black text-rose-600" x-text="log.admin_name.charAt(0)"></span>
                                                </div>
                                                <span class="text-[10px] font-black text-slate-800 uppercase" x-text="log.admin_name"></span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="text-[9px] font-black px-2 py-0.5 rounded-full border uppercase"
                                                  :class="getActionClass(log.action)"
                                                  x-text="log.action.replace(/_/g, ' ')"></span>
                                        </td>
                                        <td class="py-3 pr-4"><span class="text-[9px] text-slate-500 truncate max-w-[300px] block" x-text="log.description"></span></td>
                                        <td class="py-3"><span class="text-[9px] font-bold text-slate-400" x-text="log.logged_at"></span></td>
                                    </tr>
                                </template>
                            </template>
                            <template x-if="filteredLogs.length === 0">
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        No results found
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script>
    function dashboardPolling() {
        return {
            stats: {{ Js::from($stats) }},
            serverTime: '{{ now()->format("M d, Y | h:i A") }}',
            turnoutChart: null,
            gradeChart: null,
            tallyCharts: {},

            startPolling() {
                this.initStaticCharts();
                setTimeout(() => this.initTallyCharts(), 300);
                setInterval(() => this.fetchData(), 5000);
            },

            async fetchData() {
                try {
                    const response = await fetch('{{ route("admin.live-stats") }}');
                    const data = await response.json();
                    this.stats = data;
                    this.serverTime = new Intl.DateTimeFormat('en-US', {
                        month: 'short', day: '2-digit', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    }).format(new Date());
                    this.updateTallyCharts();
                    this.$nextTick(() => {
                        this.updateStaticCharts();
                        this.initTallyCharts();
                    });
                } catch (e) {
                    console.error('Polling error:', e);
                }
            },

            initStaticCharts() {
                const turnoutEl = document.getElementById('turnoutChart');
                const gradeEl   = document.getElementById('gradeBarChart');
                if (turnoutEl) {
                    this.turnoutChart = new Chart(turnoutEl, {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: [this.stats.turnoutPercentage, Math.max(0, 100 - this.stats.turnoutPercentage)],
                                backgroundColor: ['#4f46e5', '#f1f5f9'],
                                borderWidth: 0,
                                cutout: '85%'
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
                    });
                }
                if (gradeEl) {
                    this.gradeChart = new Chart(gradeEl, {
                        type: 'bar',
                        data: {
                            labels: this.stats.gradeBreakdown.map(g => g.name),
                            datasets: [{ label: 'Voted', data: this.stats.gradeBreakdown.map(g => parseInt(g.voted_count) || 0), backgroundColor: '#60a5fa', borderRadius: 5, barThickness: 15 }]
                        },
                        options: {
                            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { x: { grid: { display: false }, ticks: { stepSize: 1, callback: (val) => Number.isInteger(val) ? val : null } }, y: { grid: { display: false } } }
                        }
                    });
                }
            },

            initTallyCharts() {
                if (!this.stats.activeElections) return;
                this.stats.activeElections.forEach(election => {
                    election.positions.forEach(position => {
                        if (this.tallyCharts[position.id] && this.tallyCharts[position.id].canvas?.isConnected) return;
                        if (this.tallyCharts[position.id]) { this.tallyCharts[position.id].destroy(); delete this.tallyCharts[position.id]; }
                        const canvasEl = document.getElementById('chart-' + position.id);
                        if (!canvasEl) return;
                        this.tallyCharts[position.id] = new Chart(canvasEl, {
                            type: 'bar',
                            data: {
                                labels: position.candidates.map(c => c.first_name + ' ' + c.last_name),
                                datasets: [{ data: position.candidates.map(c => parseInt(c.votes_count) || 0), backgroundColor: '#4f46e5', borderRadius: 8, barThickness: 30 }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { stepSize: 1, callback: (val) => Number.isInteger(val) ? val : null } },
                                    x: { grid: { display: false }, ticks: { font: { size: 9, weight: '900' } } }
                                }
                            }
                        });
                    });
                });
            },

            updateTallyCharts() {
                if (!this.stats.activeElections) return;
                this.stats.activeElections.forEach(election => {
                    election.positions.forEach(position => {
                        const chart = this.tallyCharts[position.id];
                        if (!chart || !chart.canvas?.isConnected) return;
                        chart.data.labels = position.candidates.map(c => c.first_name + ' ' + c.last_name);
                        chart.data.datasets[0].data = position.candidates.map(c => parseInt(c.votes_count) || 0);
                        chart.update('active');
                    });
                });
            },

            updateStaticCharts() {
                if (this.turnoutChart && this.turnoutChart.canvas?.isConnected) {
                    const pct = parseFloat(this.stats.turnoutPercentage) || 0;
                    this.turnoutChart.data.datasets[0].data = [pct, Math.max(0, 100 - pct)];
                    this.turnoutChart.update();
                }
                if (this.gradeChart && this.gradeChart.canvas?.isConnected) {
                    this.gradeChart.data.labels = this.stats.gradeBreakdown.map(g => g.name);
                    this.gradeChart.data.datasets[0].data = this.stats.gradeBreakdown.map(g => parseInt(g.voted_count) || 0);
                    this.gradeChart.update();
                }
            },

            getTotalVotes(position) { return position.candidates.reduce((sum, c) => sum + (parseInt(c.votes_count) || 0), 0); },
            getMaxVotes(position) { return Math.max(...position.candidates.map(c => parseInt(c.votes_count) || 0), 0); },
            sortedCandidates(position) { return [...position.candidates].sort((a, b) => (parseInt(b.votes_count) || 0) - (parseInt(a.votes_count) || 0)); },
            isLeading(candidate, position) { const max = this.getMaxVotes(position); return max > 0 && (parseInt(candidate.votes_count) || 0) === max; },
            isTie(position) { const max = this.getMaxVotes(position); return max > 0 && position.candidates.filter(c => (parseInt(c.votes_count) || 0) === max).length > 1; },
            getPercentage(candidate, position) { const total = this.getTotalVotes(position); return total > 0 ? ((parseInt(candidate.votes_count) || 0) / total) * 100 : 0; }
        };
    }
    </script>
</x-app-layout>