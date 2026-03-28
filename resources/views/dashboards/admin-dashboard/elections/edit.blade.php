<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8" 
         x-data="electionEditor({
            election: @js($election),
            gradeLevels: @js($gradeLevels),
            sections: @js($sections)
         })" 
         x-cloak>
        
        {{-- Header Section --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-sky-50/50 p-8 rounded-[2.5rem] border border-sky-100 shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-slate-800 uppercase tracking-tight">Edit Election</h1>
                <p class="text-xs text-slate-500 italic mt-1 uppercase tracking-wider">Modify details, eligibility, and your candidate roster.</p>
            </div>
            <a href="{{ route('dashboard.elections.index') }}" class="text-[10px] font-black uppercase tracking-widest text-sky-600 hover:underline">← Back to List</a>
        </div>

        <form action="{{ route('dashboard.elections.update', $election->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit="submitForm">
            @csrf
            @method('PUT')

            {{-- 1. ELECTION INFORMATION --}}
            <div class="space-y-6 rounded-[2.5rem] border border-slate-100 p-8 bg-white shadow-xl">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Election Information</h3>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Election Title *</label>
                    <input type="text" name="title" x-model="formData.title" class="w-full rounded-2xl border-slate-200 p-4 border focus:ring-sky-400 shadow-sm" required>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Description</label>
                    <textarea name="description" x-model="formData.description" rows="3" class="w-full rounded-2xl border-slate-200 p-4 border focus:ring-sky-400 shadow-sm"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Start Date *</label>
                        <input type="datetime-local" name="start_at" x-model="formData.startDate" class="w-full rounded-2xl border-slate-200 p-4 border shadow-sm" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">End Date *</label>
                        <input type="datetime-local" name="end_at" x-model="formData.endDate" class="w-full rounded-2xl border-slate-200 p-4 border shadow-sm" required>
                    </div>
                </div>
            </div>

            {{-- 2. ELIGIBILITY SETTINGS --}}
            <div class="space-y-6 rounded-[2.5rem] border border-slate-100 p-8 bg-white shadow-xl">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Eligible Voters</h3>
                <input type="hidden" name="eligibility_type" :value="formData.eligibilityType">
                
                <div class="space-y-3">
                    {{-- All Students --}}
                    <div class="rounded-2xl border bg-white shadow-sm cursor-pointer transition-all p-5 flex items-center gap-4"
                        :class="formData.eligibilityType === 'all' ? 'border-sky-500 bg-sky-50/30 ring-2 ring-sky-100' : 'border-slate-100 hover:bg-slate-50'"
                        @click="formData.eligibilityType = 'all'">
                        <input type="radio" :checked="formData.eligibilityType === 'all'" class="h-4 w-4 text-sky-600 border-slate-300">
                        <div>
                            <h4 class="font-black text-slate-700 uppercase text-xs">All Registered Students</h4>
                        </div>
                    </div>

                    {{-- Grade Level --}}
                    <div class="rounded-2xl border bg-white shadow-sm transition-all overflow-hidden"
                        :class="formData.eligibilityType === 'grade_level' ? 'border-sky-500 ring-2 ring-sky-100' : 'border-slate-100'">
                        <div class="p-5 flex items-center gap-4 cursor-pointer" @click="formData.eligibilityType = 'grade_level'">
                            <input type="radio" :checked="formData.eligibilityType === 'grade_level'" class="h-4 w-4 text-sky-600 border-slate-300">
                            <h4 class="font-black text-slate-700 uppercase text-xs">Specific Grade Levels</h4>
                        </div>
                        <div x-show="formData.eligibilityType === 'grade_level'" class="px-12 pb-5 grid grid-cols-2 md:grid-cols-4 gap-2 border-t border-sky-50 pt-4">
                            <template x-for="grade in dbGradeLevels" :key="grade.id">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="selected_grades[]" :value="grade.id" x-model="formData.selectedGrades" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    <span class="text-[10px] font-bold text-slate-600 uppercase" x-text="grade.name"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    {{-- Sections --}}
                    <div class="rounded-2xl border bg-white shadow-sm transition-all overflow-hidden"
                        :class="formData.eligibilityType === 'section' ? 'border-sky-500 ring-2 ring-sky-100' : 'border-slate-100'">
                        <div class="p-5 flex items-center gap-4 cursor-pointer" @click="formData.eligibilityType = 'section'">
                            <input type="radio" :checked="formData.eligibilityType === 'section'" class="h-4 w-4 text-sky-600 border-slate-300">
                            <h4 class="font-black text-slate-700 uppercase text-xs">Specific Sections</h4>
                        </div>
                        <div x-show="formData.eligibilityType === 'section'" class="px-12 pb-5 grid grid-cols-1 md:grid-cols-3 gap-2 border-t border-sky-50 pt-4">
                            <template x-for="section in dbSections" :key="section.id">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="selected_sections[]" :value="section.id" x-model="formData.selectedSections" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    <span class="text-[9px] font-bold text-slate-600 uppercase" x-text="section.name"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. POSITIONS & CANDIDATES --}}
            <div class="space-y-6 rounded-[2.5rem] border border-slate-100 p-8 bg-white shadow-xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">Positions & Candidates</h3>
                    <button type="button" @click="addPositionPlaceholder" class="text-[10px] bg-sky-600 text-white px-5 py-2 rounded-full font-black uppercase tracking-widest hover:bg-sky-700 shadow-lg shadow-sky-100 transition-all">+ Add New Position</button>
                </div>

                <template x-for="(position, pIndex) in formData.positions" :key="position.id">
                    <div class="border border-sky-100 rounded-[2rem] overflow-hidden bg-white mb-8 shadow-sm">
                        {{-- Position Header --}}
                        <div class="bg-sky-50 px-8 py-5 border-b border-sky-100 flex flex-wrap justify-between items-center gap-4">
                            <div class="flex-1 min-w-[200px] grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" :name="`positions[${pIndex}][title]`" x-model="position.name" placeholder="Position Name" class="bg-white border-sky-200 rounded-xl p-2 text-sm font-black uppercase">
                                <select :name="`positions[${pIndex}][max_selection]`" x-model="position.maxSelections" class="bg-white border-sky-200 rounded-xl p-2 text-sm">
                                    <template x-for="n in 10"><option :value="n" x-text="n + ' Winner(s)'"></option></template>
                                </select>
                                <input type="hidden" :name="`positions[${pIndex}][id]`" :value="position.id">
                                <textarea :name="`positions[${pIndex}][description]`" x-model="position.description" placeholder="Description..." class="md:col-span-2 bg-white border-sky-200 rounded-xl p-2 text-xs h-12"></textarea>
                            </div>
                            <button type="button" @click="removePosition(position.id)" class="text-rose-500 hover:text-rose-700 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </button>
                        </div>

                        {{-- Candidates Section (Extra Compact) --}}
                        <div class="p-6 space-y-4 bg-slate-50/30">
                            <div class="flex justify-between items-center">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Candidates List</p>
                                <button type="button" @click="addCandidateToPosition(position.id)" class="text-[9px] font-black bg-white border border-sky-200 text-sky-600 px-3 py-1.5 rounded-lg uppercase hover:bg-sky-50 transition-all">+ Add Candidate</button>
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                <template x-for="(candidate, cIndex) in getCandidatesForPosition(position.id)" :key="candidate.id">
                                    <div class="relative p-4 border border-sky-50 rounded-2xl bg-white shadow-sm group hover:border-sky-300 transition-all">
                                        <button type="button" @click="removeCandidate(position.id, candidate.id)" class="absolute top-2 right-2 text-slate-300 hover:text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"></path></svg></button>
                                        
                                        <div class="flex flex-col md:flex-row gap-4 items-start">
                                            <input type="hidden" :name="`positions[${pIndex}][candidates][${cIndex}][id]`" :value="candidate.id">
                                            
                                            {{-- Mini Image Preview --}}
                                            <div class="w-14 h-14 flex-shrink-0">
                                                <div class="relative w-full h-full border-2 border-dashed border-sky-100 rounded-xl flex items-center justify-center bg-slate-50 overflow-hidden">
                                                    <template x-if="candidate.photo_url">
                                                        <img :src="candidate.photo_url" class="w-full h-full object-cover">
                                                    </template>
                                                    <svg x-show="!candidate.photo_url" class="w-6 h-6 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"></path></svg>
                                                    <input type="file" :name="`positions[${pIndex}][candidates][${cIndex}][photo]`" class="absolute inset-0 opacity-0 cursor-pointer">
                                                </div>
                                            </div>

                                            <div class="flex-1 space-y-2 w-full">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                    <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][first_name]`" x-model="candidate.first_name" placeholder="First Name *" class="rounded-lg border-slate-100 p-2 text-[11px] font-bold border focus:ring-sky-500" required>
                                                    <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][middle_name]`" x-model="candidate.middle_name" placeholder="Middle Name" class="rounded-lg border-slate-100 p-2 text-[11px] font-bold border focus:ring-sky-500">
                                                    <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][last_name]`" x-model="candidate.last_name" placeholder="Last Name *" class="rounded-lg border-slate-100 p-2 text-[11px] font-bold border focus:ring-sky-500" required>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                    <input type="text" :name="`positions[${pIndex}][candidates][${cIndex}][party]`" x-model="candidate.party" placeholder="Party" class="rounded-lg border-slate-100 p-2 text-[11px] border focus:ring-sky-500">
                                                    <select :name="`positions[${pIndex}][candidates][${cIndex}][grade_level_id]`" x-model="candidate.grade" class="rounded-lg border-slate-100 p-2 text-[11px]">
                                                        <option value="">Year</option>
                                                        <template x-for="grade in dbGradeLevels" :key="grade.id">
                                                            <option :value="grade.id" x-text="grade.name"></option>
                                                        </template>
                                                    </select>
                                                    <select :name="`positions[${pIndex}][candidates][${cIndex}][section_id]`" x-model="candidate.section" class="rounded-lg border-slate-100 p-2 text-[11px]">
                                                        <option value="">Section</option>
                                                        <template x-for="s in dbSections.filter(sec => sec.grade_level_id == candidate.grade)" :key="s.id">
                                                            <option :value="s.id" x-text="s.name"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <textarea :name="`positions[${pIndex}][candidates][${cIndex}][bio]`" x-model="candidate.bio" rows="1" class="rounded-lg border-slate-100 p-2 text-[10px] border" placeholder="Candidate Bio"></textarea>
                                                    <textarea :name="`positions[${pIndex}][candidates][${cIndex}][platform]`" x-model="candidate.platform" rows="1" class="rounded-lg border-slate-100 p-2 text-[10px] border" placeholder="Candidate Platform"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Footer Controls --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-50 mt-10">
                <a href="{{ route('dashboard.elections.index') }}" class="px-8 py-4 font-black uppercase text-xs tracking-widest text-slate-400 hover:text-slate-600 transition-all">Cancel</a>
                <button type="submit" class="px-10 py-4 bg-sky-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest shadow-xl shadow-sky-100 hover:bg-sky-700 transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('electionEditor', (data) => ({
            dbGradeLevels: data.gradeLevels || [],
            dbSections: data.sections || [],
            formData: {
                title: '', description: '', startDate: '', endDate: '',
                eligibilityType: 'all', selectedGrades: [], selectedSections: [],
                positions: [], candidates: {}, 
            },

            init() {
                const el = data.election;
                this.formData.title = el.title;
                this.formData.description = el.description || '';
                this.formData.startDate = el.start_at ? new Date(el.start_at).toISOString().slice(0, 16) : '';
                this.formData.endDate = el.end_at ? new Date(el.end_at).toISOString().slice(0, 16) : '';
                this.formData.eligibilityType = el.eligibility_type || 'all';

                let meta = el.eligibility_metadata;
                if (typeof meta === 'string') { try { meta = JSON.parse(meta); } catch(e) { meta = []; } }
                meta = meta || [];

                if (this.formData.eligibilityType === 'grade_level') {
                    this.formData.selectedGrades = (Array.isArray(meta) ? meta : (meta.grades || [])).map(Number);
                }
                if (this.formData.eligibilityType === 'section') {
                    this.formData.selectedSections = (Array.isArray(meta) ? meta : (meta.sections || [])).map(Number);
                }

                if (el.positions) {
                    el.positions.forEach(pos => {
                        const pid = pos.id;
                        this.formData.positions.push({ id: pid, name: pos.title, description: pos.description, maxSelections: pos.max_selection });
                        this.formData.candidates[pid] = [];
                        if (pos.candidates) {
                            pos.candidates.forEach(cand => {
                                this.formData.candidates[pid].push({
                                    id: cand.id, first_name: cand.first_name, middle_name: cand.middle_name, last_name: cand.last_name,
                                    grade: cand.grade_level_id, section: cand.section_id, party: cand.party, platform: cand.platform, bio: cand.bio,
                                    photo_url: cand.photo_path ? `/storage/${cand.photo_path}` : null
                                });
                            });
                        }
                    });
                }
            },

            addPositionPlaceholder() {
                const id = 'new-pos-' + Date.now();
                this.formData.positions.push({ id: id, name: '', description: '', maxSelections: 1 });
                this.formData.candidates[id] = [];
            },

            removePosition(id) {
                this.formData.positions = this.formData.positions.filter(p => p.id !== id);
                delete this.formData.candidates[id];
            },

            addCandidateToPosition(pid) {
                const id = 'new-cand-' + Date.now();
                if (!this.formData.candidates[pid]) this.formData.candidates[pid] = [];
                this.formData.candidates[pid].push({ id: id, first_name: '', middle_name: '', last_name: '', grade: '', section: '', party: '', platform: '', bio: '', photo_url: null });
            },

            removeCandidate(pid, cid) {
                this.formData.candidates[pid] = this.formData.candidates[pid].filter(c => c.id !== cid);
            },

            getCandidatesForPosition(pid) { return this.formData.candidates[pid] || []; },

            submitForm(e) {
                if (!this.formData.title.trim()) { alert('Please enter an election title'); e.preventDefault(); return; }
                if (this.formData.positions.length === 0) { alert('Please add at least one position'); e.preventDefault(); return; }
            }
        }));
    });
</script>
</x-app-layout>