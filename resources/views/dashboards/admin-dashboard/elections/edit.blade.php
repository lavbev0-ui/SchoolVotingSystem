<x-app-layout>
<div class="container mx-auto py-10 px-4 max-w-5xl" 
     x-data="electionEditor({
        election: @js($election),
        gradeLevels: @js($gradeLevels),
        sections: @js($sections)
     })" 
     x-cloak>
    
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Edit Election</h2>
                <p class="text-muted-foreground text-gray-500">Make changes to the election details, positions, and candidates</p>
            </div>
        </div>

        <form action="{{ route('dashboard.elections.update', $election->id) }}" method="POST" class="space-y-8" @submit.prevent="submitForm">
            @csrf
            @method('PUT')
            
            {{-- This hidden input carries the JSON payload for the controller to process --}}
            <input type="hidden" name="election_data" :value="JSON.stringify(getDataForSubmit())">

            {{-- 1. ELECTION INFORMATION --}}
            <div class="space-y-4 rounded-lg border p-6 bg-white shadow-sm">
                <h3 class="text-lg font-semibold">Election Information</h3>
                
                <div class="space-y-2">
                    <label for="title" class="text-sm font-medium leading-none">Election Title *</label>
                    <input type="text" id="title" x-model="formData.title" class="flex h-10 w-full rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Supreme Student Government Election 2026">
                </div>

                <div class="space-y-2">
                    <label for="description" class="text-sm font-medium leading-none">Description</label>
                    <textarea id="description" x-model="formData.description" rows="3" class="flex w-full rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe the purpose..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">Start Date *</label>
                        <input type="datetime-local" x-model="formData.startDate" class="flex h-10 w-full rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none">End Date *</label>
                        <input type="datetime-local" x-model="formData.endDate" class="flex h-10 w-full rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <hr class="border-gray-200">

            {{-- 2. ELIGIBILITY SETTINGS --}}
            <div class="space-y-4 rounded-lg border p-6 bg-white shadow-sm">
                <h3 class="text-lg font-semibold">Eligible Voters</h3>
                
                <div class="space-y-3">
                    {{-- Option: All Students --}}
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm cursor-pointer transition-all hover:bg-gray-50"
                        :class="{ 'border-blue-500 ring-2 ring-blue-500': formData.eligibilityType === 'all' }"
                        @click="formData.eligibilityType = 'all'">
                        <div class="p-4 flex items-center gap-3">
                            <input type="radio" name="eligibility" value="all" :checked="formData.eligibilityType === 'all'" class="h-4 w-4 text-blue-600">
                            <div>
                                <h4 class="font-semibold">All Students</h4>
                                <p class="text-sm text-gray-500">All registered students can vote</p>
                            </div>
                        </div>
                    </div>

                    {{-- Option: Grade Level --}}
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm cursor-pointer transition-all hover:bg-gray-50"
                        :class="{ 'border-blue-500 ring-2 ring-blue-500': formData.eligibilityType === 'grade_level' }"
                        @click="formData.eligibilityType = 'grade_level'">
                        <div class="p-4 flex items-center gap-3">
                            <input type="radio" name="eligibility" value="grade_level" :checked="formData.eligibilityType === 'grade_level'" class="h-4 w-4 text-blue-600">
                            <div>
                                <h4 class="font-semibold">Specific Grade Levels</h4>
                                <p class="text-sm text-gray-500">Only students from selected grades</p>
                            </div>
                        </div>
                        <div x-show="formData.eligibilityType === 'grade_level'" class="px-4 pb-4 pt-0 animate-in fade-in zoom-in-95">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-3">
                                <template x-for="grade in dbGradeLevels" :key="grade.id">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" :id="'grade-'+grade.id" :value="grade.id" x-model="formData.selectedGrades" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <label :for="'grade-'+grade.id" class="text-sm cursor-pointer select-none" x-text="grade.name"></label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Option: Sections --}}
                    <div class="rounded-xl border bg-card text-card-foreground shadow-sm cursor-pointer transition-all hover:bg-gray-50"
                        :class="{ 'border-blue-500 ring-2 ring-blue-500': formData.eligibilityType === 'section' }"
                        @click="formData.eligibilityType = 'section'">
                        <div class="p-4 flex items-center gap-3">
                            <input type="radio" name="eligibility" value="section" :checked="formData.eligibilityType === 'section'" class="h-4 w-4 text-blue-600">
                            <div>
                                <h4 class="font-semibold">Specific Sections</h4>
                                <p class="text-sm text-gray-500">Only students from selected sections</p>
                            </div>
                        </div>
                        <div x-show="formData.eligibilityType === 'section'" class="px-4 pb-4 pt-0 animate-in fade-in zoom-in-95">
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-3">
                                <template x-for="section in dbSections" :key="section.id">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" :id="'section-'+section.id" :value="section.id" x-model="formData.selectedSections" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <label :for="'section-'+section.id" class="text-sm cursor-pointer select-none" x-text="section.name"></label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <hr class="border-gray-200">

            {{-- 3. POSITIONS --}}
            <div class="space-y-4 rounded-lg border p-6 bg-white shadow-sm">
                <h3 class="text-lg font-semibold">Positions</h3>

                <div class="rounded-lg border bg-gray-50 p-4">
                    <h4 class="font-medium mb-4">Add New Position</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Position Name *</label>
                            <input type="text" x-model="newPosition.name" class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="e.g., President">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Maximum Winners</label>
                            <select x-model="newPosition.maxSelections" class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="1">1 (Single winner)</option>
                                <option value="2">2 winners</option>
                                <option value="3">3 winners</option>
                                <option value="4">4 winners</option>
                                <option value="5">5 winners</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2 mb-4">
                        <label class="text-sm font-medium">Description</label>
                        <input type="text" x-model="newPosition.description" class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Brief description">
                    </div>
                    <button type="button" @click="addPosition" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-black text-white hover:bg-black/90 h-10 px-4 py-2 w-full gap-2">
                        Add Position
                    </button>
                </div>

                <template x-if="formData.positions.length > 0">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-500">Current Positions (<span x-text="formData.positions.length"></span>)</label>
                        <div class="space-y-2">
                            <template x-for="(position, index) in formData.positions" :key="position.id">
                                <div class="flex items-center justify-between p-3 border rounded-lg bg-white">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="font-medium" x-text="position.name"></p>
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold" x-text="'Max ' + position.maxSelections"></span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1" x-text="position.description" x-show="position.description"></p>
                                    </div>
                                    <button type="button" @click="removePosition(position.id)" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium hover:bg-gray-100 hover:text-red-600 h-9 w-9 text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <hr class="border-gray-200">

            {{-- 4. CANDIDATES --}}
            <div class="space-y-4 rounded-lg border p-6 bg-white shadow-sm">
                <h3 class="text-lg font-semibold">Candidates</h3>

                <template x-if="formData.positions.length === 0">
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6 pt-0 py-8 text-center text-gray-500">
                            Please add positions first before adding candidates
                        </div>
                    </div>
                </template>

                <template x-if="formData.positions.length > 0">
                    <div class="space-y-6">
                        <div class="rounded-lg border bg-gray-50 p-4">
                            <h4 class="font-medium mb-4">Add New Candidate</h4>
                            
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Position *</label>
                                    <select x-model="selectedPositionId" class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select a position</option>
                                        <template x-for="pos in formData.positions" :key="pos.id">
                                            <option :value="pos.id" x-text="pos.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Candidate Name *</label>
                                    <input type="text" x-model="newCandidate.name" class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Full name">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- GRADE LEVEL SELECT --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Grade Level</label>
                                        <select 
                                            x-model="newCandidate.grade"
                                            @change="newCandidate.section = ''" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                            
                                            <option value="">Select Grade</option>
                                            <template x-for="grade in dbGradeLevels" :key="grade.id">
                                                <option :value="grade.id" x-text="grade.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    {{-- SECTION SELECT --}}
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700">Section</label>
                                        <select 
                                            x-model="newCandidate.section"
                                            :disabled="!newCandidate.grade"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-400">
                                            
                                            <option value="">Select Section</option>
                                            
                                            <template x-for="section in dbSections.filter(s => s.grade_level_id == newCandidate.grade)" :key="section.id">
                                                <option :value="section.id" x-text="section.name"></option>
                                            </template>
                                            
                                            <template x-if="newCandidate.grade && dbSections.filter(s => s.grade_level_id == newCandidate.grade).length === 0">
                                                <option disabled>No sections found</option>
                                            </template>
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Party / Affiliation</label>
                                    <input type="text" x-model="newCandidate.party" class="flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="e.g., Independent, Unity Party">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-medium">Platform / Vision</label>
                                    <textarea x-model="newCandidate.platform" rows="2" class="flex w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Brief description..."></textarea>
                                </div>

                                <button type="button" @click="addCandidate" :disabled="!selectedPositionId || !newCandidate.name.trim()" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-black text-white hover:bg-black/90 h-10 px-4 py-2 w-full gap-2">
                                    Add Candidate
                                </button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <template x-for="position in formData.positions" :key="position.id">
                                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                                    <div class="flex flex-col space-y-1.5 p-6 pb-2">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-base font-semibold leading-none tracking-tight" x-text="position.name"></h3>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <span x-text="getCandidatesForPosition(position.id).length"></span> candidate(s)
                                                    <span x-show="getCandidatesForPosition(position.id).length < 2" class="text-red-500 ml-1 font-medium">(Need at least 2)</span>
                                                </p>
                                            </div>
                                            <span x-show="getCandidatesForPosition(position.id).length >= 2" class="inline-flex items-center rounded-full border border-transparent px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-700">
                                                Ready
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-6 pt-2 space-y-2">
                                        <template x-for="candidate in getCandidatesForPosition(position.id)" :key="candidate.id">
                                            <div class="flex items-start justify-between gap-4 p-3 border rounded-lg">
                                                <div class="flex-1">
                                                    <p class="font-medium" x-text="candidate.name"></p>
                                                    <div class="flex flex-wrap gap-2 mt-1">
                                                        <span x-show="candidate.party" x-text="candidate.party" class="inline-flex items-center rounded-full border border-transparent bg-gray-100 text-gray-900 px-2.5 py-0.5 text-xs font-semibold"></span>
                                                    </div>
                                                    <p x-show="candidate.platform" x-text="candidate.platform" class="text-sm text-gray-500 mt-1"></p>
                                                </div>
                                                <button type="button" @click="removeCandidate(position.id, candidate.id)" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium hover:bg-gray-100 hover:text-red-600 h-9 w-9 text-gray-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('dashboard.elections.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-gray-100 h-10 px-4 py-2">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-black text-white hover:bg-black/90 h-10 px-4 py-2">
                    Save Changes
                </button>
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
                title: '',
                description: '',
                startDate: '',
                endDate: '',
                eligibilityType: 'all',
                selectedGrades: [],
                selectedSections: [],
                positions: [],
                candidates: {}, 
            },

            newPosition: { name: '', description: '', maxSelections: 1 },
            newCandidate: { name: '', grade: '', section: '', party: '', platform: '' },
            selectedPositionId: '',

            init() {
                const election = data.election;

                this.formData.title = election.title;
                this.formData.description = election.bio || '';
                // Format dates for datetime-local input
                this.formData.startDate = election.start_at ? new Date(election.start_at).toISOString().slice(0, 16) : '';
                this.formData.endDate = election.end_at ? new Date(election.end_at).toISOString().slice(0, 16) : '';
                this.formData.eligibilityType = election.eligibility_type || 'all';

                let metadata = election.eligibility_metadata; 
                
                // Safety check: ensure metadata is parsed if it came as a string
                if (typeof metadata === 'string') {
                    try { metadata = JSON.parse(metadata); } catch(e) { metadata = []; }
                }
                // Default to empty array/object if null
                metadata = metadata || [];

                // --- FIX STARTS HERE ---
                
                // Logic for Grade Levels
                if (this.formData.eligibilityType === 'grade_level') {
                    // Check if metadata IS the array (matches your DB screenshots) 
                    // OR if it's inside a 'grades' property (matches your submit logic)
                    let sourceArray = Array.isArray(metadata) ? metadata : (metadata.grades || []);
                    
                    // Convert all IDs to Numbers to ensure they match the checkbox values
                    this.formData.selectedGrades = sourceArray.map(id => Number(id));
                }
                
                // Logic for Sections
                if (this.formData.eligibilityType === 'section') {
                    let sourceArray = Array.isArray(metadata) ? metadata : (metadata.sections || []);
                    this.formData.selectedSections = sourceArray.map(id => Number(id));
                }

                // --- FIX ENDS HERE ---
                
                if (election.positions) {
                    election.positions.forEach(pos => {
                        const posId = pos.id; 

                        this.formData.positions.push({
                            id: posId,
                            name: pos.title, 
                            description: pos.description,
                            maxSelections: pos.max_selection 
                        });

                        this.formData.candidates[posId] = [];

                        if (pos.candidates) {
                            pos.candidates.forEach(cand => {
                                this.formData.candidates[posId].push({
                                    id: cand.id,
                                    name: cand.name,
                                    grade: cand.grade_level_id, 
                                    section: cand.section_id,   
                                    party: cand.party,
                                    platform: cand.platform
                                });
                            });
                        }
                    });
                }
            },

            addPosition() {
                if (this.newPosition.name.trim()) {
                    const id = 'new-pos-' + Date.now();
                    this.formData.positions.push({
                        id: id,
                        ...this.newPosition
                    });
                    this.formData.candidates[id] = [];
                    this.newPosition = { name: '', description: '', maxSelections: 1 };
                }
            },

            removePosition(id) {
                this.formData.positions = this.formData.positions.filter(p => p.id !== id);
                delete this.formData.candidates[id];
            },

            addCandidate() {
                if (this.selectedPositionId && this.newCandidate.name.trim()) {
                    const id = 'new-cand-' + Date.now();
                    const candidate = {
                        id: id,
                        ...this.newCandidate
                    };

                    if (!this.formData.candidates[this.selectedPositionId]) {
                        this.formData.candidates[this.selectedPositionId] = [];
                    }
                    
                    this.formData.candidates[this.selectedPositionId].push(candidate);
                    this.newCandidate = { name: '', grade: '', section: '', party: '', platform: '' };
                }
            },

            removeCandidate(posId, candId) {
                if (this.formData.candidates[posId]) {
                    this.formData.candidates[posId] = this.formData.candidates[posId].filter(c => c.id !== candId);
                }
            },

            getCandidatesForPosition(posId) {
                return this.formData.candidates[posId] || [];
            },

            getDataForSubmit() {
                let metadata = {};
                if (this.formData.eligibilityType === 'grade_level') {
                    metadata = { grades: this.formData.selectedGrades };
                } else if (this.formData.eligibilityType === 'section') {
                    metadata = { sections: this.formData.selectedSections };
                } 

                return {
                    ...this.formData,
                    eligibility_metadata: metadata
                };
            },
            
            submitForm(e) {
                if (!this.formData.title.trim()) {
                    alert('Please enter an election title'); return;
                }
                if (this.formData.positions.length === 0) {
                    alert('Please add at least one position'); return;
                }
                const invalidPositions = this.formData.positions.filter(pos => 
                    !this.formData.candidates[pos.id] || this.formData.candidates[pos.id].length < 2
                );
                if (invalidPositions.length > 0) {
                    alert('Each position must have at least 2 candidates'); return;
                }
                e.target.submit();
            }
        }));
    });
</script>
</x-app-layout>