<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4" x-data="electionForm()" x-cloak>

        <div class="mb-6 text-center md:text-left">
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Create New Election</h1>
            <p class="mt-1 text-[10px] text-slate-500 italic uppercase tracking-wider">Setup details, eligibility, positions, and candidates.</p>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between w-full">
                <template x-for="(stepObj, index) in steps" :key="index">
                    <div class="flex items-center flex-1 relative">
                        <div class="flex flex-col items-center flex-1 z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mb-1 transition-all duration-300 border-2 text-xs"
                                 :class="{
                                     'bg-sky-500 border-sky-500 text-white shadow-md': currentStep > stepObj.id,
                                     'bg-white border-sky-500 text-sky-600 ring-2 ring-sky-50': currentStep === stepObj.id,
                                     'bg-gray-100 border-gray-300 text-gray-400': currentStep < stepObj.id
                                 }">
                                <span x-text="stepObj.id" class="font-bold"></span>
                            </div>
                            <p class="text-[8px] font-black text-center uppercase tracking-widest"
                               :class="currentStep >= stepObj.id ? 'text-sky-600' : 'text-gray-400'"
                               x-text="stepObj.title"></p>
                        </div>
                        <div x-show="index < steps.length - 1"
                             class="absolute top-4 left-1/2 w-full h-0.5 -z-0"
                             :class="currentStep > stepObj.id ? 'bg-sky-500' : 'bg-gray-200'">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ADD PARTYLIST MODAL --}}
        <div x-show="showPartyModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 overflow-hidden" @click.outside="showPartyModal = false">
                <div class="px-8 py-6 border-b border-slate-100">
                    <h2 class="text-sm font-black uppercase tracking-widest text-slate-800">New Partylist</h2>
                    <p class="text-[9px] text-slate-400 uppercase mt-1">This will be added to the dropdown.</p>
                </div>
                <div class="p-8 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Party Logo</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 border-2 border-dashed border-sky-200 rounded-xl overflow-hidden bg-slate-50 flex items-center justify-center relative">
                                <img id="modal-logo-preview" class="w-full h-full object-cover hidden">
                                <span id="modal-logo-placeholder" class="text-[8px] font-black text-sky-300 uppercase">Logo</span>
                                <input type="file" id="modal-logo-input" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" @change="previewModalLogo($event)">
                            </div>
                            <p class="text-[9px] text-slate-400 italic">JPG, PNG, WEBP. Max 2MB.</p>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Party Name *</label>
                        <input type="text" x-model="newParty.name" placeholder="e.g., Sulo Party" class="w-full rounded-xl border-slate-200 p-3 text-sm border focus:ring-sky-400">
                        <p x-show="partyError" x-text="partyError" class="text-rose-500 text-[9px] mt-1"></p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Description</label>
                        <textarea x-model="newParty.description" rows="2" placeholder="Brief description..." class="w-full rounded-xl border-slate-200 p-3 text-sm border focus:ring-sky-400"></textarea>
                    </div>
                </div>
                <div class="bg-slate-50 px-8 py-5 border-t flex items-center justify-between">
                    <button type="button" @click="showPartyModal = false" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition">Cancel</button>
                    <button type="button" @click="saveParty()" :disabled="partySaving" class="px-8 py-3 bg-sky-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg hover:bg-sky-700 transition active:scale-95 disabled:opacity-50">
                        <span x-show="!partySaving">Save Party</span>
                        <span x-show="partySaving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- REVIEW MODAL --}}
        <div x-show="showReviewModal" x-cloak x-transition
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-8 py-5 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-widest text-slate-900">Review Before Launching</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Double-check all candidates before confirming.</p>
                    </div>
                    <button @click="showReviewModal = false"
                            class="p-2 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Election Info --}}
                <div class="px-8 py-4 bg-slate-50 border-b border-slate-100">
                    <p class="text-xs font-black text-slate-800 uppercase" x-text="formData.title"></p>
                    <p class="text-[10px] text-slate-400 mt-0.5"
                       x-text="formData.start_at + ' → ' + formData.end_at"></p>
                </div>

                {{-- Candidates List --}}
                <div class="overflow-auto flex-1 px-8 py-4 space-y-4">
                    <template x-for="(pos, posIndex) in formData.positions" :key="posIndex">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-1.5 h-4 bg-sky-500 rounded-full"></div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-sky-700" x-text="pos.title"></p>
                                <span class="text-[8px] font-black bg-sky-100 text-sky-600 px-2 py-0.5 rounded-full uppercase"
                                      x-text="pos.candidates.length + ' candidate(s)'"></span>
                            </div>

                            <div class="space-y-2 ml-4">
                                <template x-for="(cand, candIndex) in pos.candidates" :key="candIndex">
                                    <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        {{-- Photo --}}
                                        <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 shrink-0 bg-slate-100 flex items-center justify-center">
                                            <template x-if="cand.photoPreview">
                                                <img :src="cand.photoPreview" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="cand.drivePhotoUrl && !cand.photoPreview">
                                                <img :src="cand.drivePhotoUrl" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!cand.photoPreview && !cand.drivePhotoUrl">
                                                <span class="text-[10px] font-black text-slate-400 uppercase"
                                                      x-text="cand.first_name ? cand.first_name.charAt(0) : '?'"></span>
                                            </template>
                                        </div>

                                        {{-- Info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[11px] font-black text-slate-900 uppercase"
                                               x-text="cand.first_name + ' ' + (cand.middle_name || '') + ' ' + cand.last_name"></p>
                                            <p class="text-[9px] text-sky-500 font-bold uppercase mt-0.5"
                                               x-text="cand.party || 'Independent'"></p>
                                            <div class="flex gap-2 mt-1 flex-wrap">
                                                <span class="text-[8px] font-black px-1.5 py-0.5 rounded-md uppercase"
                                                      :class="cand.bio ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'"
                                                      x-text="cand.bio ? '✓ Bio' : '✗ No Bio'"></span>
                                                <span class="text-[8px] font-black px-1.5 py-0.5 rounded-md uppercase"
                                                      :class="cand.manifesto ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'"
                                                      x-text="cand.manifesto ? '✓ Platform' : '✗ No Platform'"></span>
                                                <span class="text-[8px] font-black px-1.5 py-0.5 rounded-md uppercase"
                                                      :class="cand.grade_level_id && cand.section_id ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'"
                                                      x-text="cand.grade_level_id && cand.section_id ? '✓ Grade & Section' : '✗ Missing Grade/Section'"></span>
                                                <span class="text-[8px] font-black px-1.5 py-0.5 rounded-md uppercase"
                                                      :class="(cand.photoPreview || cand.drivePhotoUrl) ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'"
                                                      x-text="(cand.photoPreview || cand.drivePhotoUrl) ? '✓ Photo' : '⚠ No Photo'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-8 py-5 border-t border-slate-100 flex gap-3">
                    <button @click="showReviewModal = false"
                            class="flex-1 py-3 bg-slate-100 text-slate-700 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-200 transition-all">
                        Go Back & Edit
                    </button>
                    <button @click="confirmLaunch()"
                            :disabled="isSubmitting"
                            class="flex-1 py-3 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all disabled:opacity-50"
                            x-text="isSubmitting ? 'Launching...' : '🚀 Confirm & Launch'">
                    </button>
                </div>
            </div>
        </div>

        {{-- SUCCESS MODAL --}}
        <div x-show="showSuccessModal" x-cloak x-transition
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm p-8 text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-2">Election Launched!</h2>
                <p class="text-sm text-slate-500 mb-6">The election has been successfully created and is now active.</p>
                <a href="{{ route('admin.elections.index') }}"
                   class="block w-full py-3 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all">
                    Go to Elections
                </a>
            </div>
        </div>

        <form id="electionForm" action="{{ route('admin.elections.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white shadow-xl rounded-3xl border border-slate-100 overflow-hidden">
            @csrf

            <div class="p-6 md:p-8 min-h-[400px]">

                {{-- STEP 1: DETAILS --}}
                <div x-show="currentStep === 1" x-transition class="space-y-5">

                    {{-- ELECTION TITLE --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">
                            Election Title <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[10px] text-slate-400 ml-1 -mt-1 italic">
                        </p>
                        <input type="text"
                               name="title"
                               x-model="formData.title"
                               class="w-full rounded-xl border-slate-200 p-3 text-sm border focus:ring-sky-400 focus:border-sky-400"
                               required>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">
                            General Description
                            <span class="text-[9px] font-medium text-slate-400 normal-case tracking-normal"></span>
                        </label>
                        <p class="text-[10px] text-slate-400 ml-1 -mt-1 italic">
                        </p>
                        <textarea name="description"
                                  rows="2"
                                  x-model="formData.description"
                                  class="w-full rounded-xl border-slate-200 p-3 text-sm border focus:ring-sky-400 focus:border-sky-400"></textarea>
                    </div>

                    {{-- DATE RANGE --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">
                                Start Date <span class="text-rose-500">*</span>
                            </label>
                            <input type="datetime-local"
                                   name="start_at"
                                   x-model="formData.start_at"
                                   class="w-full rounded-xl border-slate-200 p-3 text-sm border shadow-sm focus:ring-sky-400"
                                   required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-widest ml-1">
                                End Date <span class="text-rose-500">*</span>
                            </label>
                            <input type="datetime-local"
                                   name="end_at"
                                   x-model="formData.end_at"
                                   class="w-full rounded-xl border-slate-200 p-3 text-sm border shadow-sm focus:ring-sky-400"
                                   required>
                        </div>
                    </div>

                </div>

                {{-- STEP 2: ELIGIBILITY --}}
                <div x-show="currentStep === 2" x-transition class="space-y-4">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight mb-4 border-b pb-2">Voter Eligibility</h3>
                    <input type="hidden" name="eligibility_type" value="all">
                    <div class="p-6 border-2 border-sky-500 bg-sky-50/50 ring-2 ring-sky-100 rounded-2xl flex items-center gap-4">
                        <div class="w-10 h-10 bg-sky-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-black text-slate-800 uppercase text-[11px] tracking-widest">Open to All Registered Students</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">All active voters in the system can participate in this election.</p>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: POSITIONS --}}
                <div x-show="currentStep === 3" x-transition class="space-y-4">

                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-800">Import via CSV</p>
                                <p class="text-[9px] text-amber-600 mt-0.5">Mag-upload ng CSV para auto-create ng positions at candidates.</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                                <button type="button" @click="downloadTemplate()"
                                        class="text-[9px] font-black uppercase tracking-widest text-amber-700 border border-amber-300 bg-white hover:bg-amber-100 px-3 py-2 rounded-xl transition active:scale-95 shadow-sm whitespace-nowrap">
                                    Download Template
                                </button>
                                <label class="cursor-pointer bg-amber-500 hover:bg-amber-600 text-white text-[9px] font-black uppercase tracking-widest px-3 py-2 rounded-xl transition active:scale-95 shadow whitespace-nowrap">
                                    Upload CSV
                                    <input type="file" accept=".csv,.txt" class="hidden" @change="importCSV($event)">
                                </label>
                            </div>
                        </div>

                        <div x-show="csvStatus !== ''"
                             class="mt-2 text-[9px] font-semibold rounded-lg px-3 py-2"
                             :class="csvHasError ? 'bg-rose-100 text-rose-700' : 'bg-green-100 text-green-700'"
                             x-text="csvStatus">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 my-2">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">or add position manually</span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <div class="bg-sky-50 p-6 rounded-[2rem] border border-sky-100 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-widest text-sky-900 ml-1">Position Title</label>
                                <input type="text" x-model="newPosition.title" placeholder="e.g., President" class="w-full rounded-xl border-sky-200 p-3 text-xs shadow-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-widest text-sky-900 ml-1">Max Selection</label>
                                <select x-model="newPosition.max_selection" class="w-full rounded-xl border-sky-200 p-3 text-xs">
                                    <template x-for="n in 12"><option :value="n" x-text="n"></option></template>
                                </select>
                            </div>
                            <div class="md:col-span-2 space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-widest text-sky-900 ml-1">Position Description (Optional)</label>
                                <textarea x-model="newPosition.description" rows="2" placeholder="Responsibilities..." class="w-full rounded-xl border-sky-200 p-3 text-xs shadow-sm"></textarea>
                            </div>
                        </div>
                        <button type="button" @click="addPosition()" class="mt-4 w-full bg-sky-600 text-white py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-sky-200">Add Position Slot</button>
                    </div>

                    <div class="space-y-2 mt-4">
                        <template x-for="(pos, index) in formData.positions" :key="index">
                            <div class="p-4 border border-slate-100 rounded-2xl flex justify-between items-center bg-white shadow-sm border-l-4 border-l-sky-500">
                                <div class="flex-1 pr-4">
                                    <input type="hidden" :name="`positions[${index}][title]`" :value="pos.title">
                                    <input type="hidden" :name="`positions[${index}][max_selection]`" :value="pos.max_selection">
                                    <input type="hidden" :name="`positions[${index}][description]`" :value="pos.description">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-black text-slate-800 text-sm uppercase" x-text="pos.title"></h4>
                                        <span class="text-[9px] font-black bg-sky-100 text-sky-600 px-2 py-0.5 rounded-full uppercase"
                                              x-text="pos.max_selection + ' Slot(s)'"></span>
                                        <span x-show="pos.fromCSV" class="text-[8px] font-black bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full uppercase">CSV</span>
                                        <span class="text-[8px] font-black bg-green-100 text-green-600 px-2 py-0.5 rounded-full uppercase"
                                              x-text="(pos.candidates ? pos.candidates.length : 0) + ' candidate(s)'"></span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 italic mt-1 truncate" x-text="pos.description || 'No description'"></p>
                                </div>
                                <button type="button" @click="removePosition(index)"
                                        class="text-rose-500 font-bold uppercase text-[9px] hover:bg-rose-50 px-3 py-1 rounded-lg flex-shrink-0">
                                    Remove
                                </button>
                            </div>
                        </template>
                        <div x-show="formData.positions.length === 0"
                             class="text-center py-6 text-[9px] font-black uppercase text-slate-300 tracking-widest border-2 border-dashed border-slate-200 rounded-2xl">
                            No positions yet — import via CSV or add manually above.
                        </div>
                    </div>
                </div>

                {{-- STEP 4: CANDIDATES --}}
                <div x-show="currentStep === 4" x-transition class="space-y-4">

                    <div class="flex justify-between items-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                            Review and Complete each Candidate.
                        </p>
                        <button type="button" @click="showPartyModal = true"
                                class="text-[9px] font-black uppercase tracking-widest text-sky-600 border border-sky-200 bg-sky-50 hover:bg-sky-100 px-4 py-2 rounded-xl transition">
                            + New Partylist
                        </button>
                    </div>

                    <template x-for="(pos, posIndex) in formData.positions" :key="posIndex">
                        <div class="border border-slate-100 rounded-2xl overflow-hidden bg-white shadow-sm mb-4">
                            <div class="bg-sky-50 px-5 py-3 flex justify-between items-center border-b border-sky-100">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-black text-slate-800 uppercase text-[10px] tracking-widest" x-text="pos.title"></h4>
                                    <span x-show="pos.fromCSV" class="text-[8px] font-black bg-amber-100 text-amber-600 px-2 py-0.5 rounded-full uppercase">CSV</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[8px] bg-sky-100 text-sky-600 px-2 py-1 rounded-full font-black uppercase"
                                          x-text="pos.candidates.length + ' candidate(s)'"></span>
                                    <button type="button" @click="addCandidateToPosition(posIndex)"
                                            class="text-[8px] bg-white text-sky-600 px-3 py-1.5 rounded-lg font-black uppercase tracking-widest hover:bg-sky-100 transition border border-sky-100 shadow-sm">
                                        + Add Manually
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 space-y-3">
                                <template x-for="(candidate, candIndex) in pos.candidates" :key="candIndex">
                                    <div class="relative p-4 border border-slate-100 rounded-2xl bg-slate-50/30 hover:bg-white hover:shadow-md transition-all">
                                        <button type="button" @click="removeCandidate(posIndex, candIndex)"
                                                class="absolute top-2 right-2 text-slate-300 hover:text-rose-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"></path>
                                            </svg>
                                        </button>

                                        <div x-show="candidate.fromCSV"
                                             class="inline-block mb-2 text-[8px] font-black uppercase bg-green-100 text-green-600 px-2 py-0.5 rounded-full">
                                            From CSV
                                        </div>

                                        <div class="flex flex-col md:flex-row gap-4">
                                            <div class="w-16 h-16 flex-shrink-0 mx-auto md:mx-0">
                                                <div class="relative w-full h-full border-2 border-dashed border-sky-200 rounded-xl bg-white overflow-hidden">
                                                    <img x-show="candidate.drivePhotoUrl && !candidate.photoPreview"
                                                         :src="candidate.drivePhotoUrl"
                                                         class="absolute inset-0 w-full h-full object-cover"
                                                         x-on:error="candidate.drivePhotoUrl = null">
                                                    <img x-show="candidate.photoPreview"
                                                         :src="candidate.photoPreview"
                                                         class="absolute inset-0 w-full h-full object-cover">
                                                    <input type="file"
                                                           :name="`positions[${posIndex}][candidates][${candIndex}][photo]`"
                                                           @change="handleImagePreview($event, posIndex, candIndex)"
                                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                    <div x-show="!candidate.photoPreview && !candidate.drivePhotoUrl"
                                                         class="flex flex-col items-center justify-center h-full">
                                                        <span class="text-[8px] font-black text-sky-300 uppercase">Photo</span>
                                                    </div>
                                                    <div x-show="candidate.drivePhotoUrl && !candidate.photoPreview"
                                                         class="absolute bottom-0 left-0 right-0 bg-green-500 text-white text-[6px] font-black text-center py-0.5">
                                                        Drive
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-1 space-y-2">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                    <input type="text"
                                                           :name="`positions[${posIndex}][candidates][${candIndex}][first_name]`"
                                                           x-model="candidate.first_name"
                                                           placeholder="First Name *"
                                                           class="rounded-lg border-slate-200 p-2 text-[11px] shadow-sm focus:ring-sky-500" required>
                                                    <input type="text"
                                                           :name="`positions[${posIndex}][candidates][${candIndex}][middle_name]`"
                                                           x-model="candidate.middle_name"
                                                           placeholder="Middle Name"
                                                           class="rounded-lg border-slate-200 p-2 text-[11px] shadow-sm">
                                                    <input type="text"
                                                           :name="`positions[${posIndex}][candidates][${candIndex}][last_name]`"
                                                           x-model="candidate.last_name"
                                                           placeholder="Last Name *"
                                                           class="rounded-lg border-slate-200 p-2 text-[11px] shadow-sm" required>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                                    <select :name="`positions[${posIndex}][candidates][${candIndex}][grade_level_id]`"
                                                            x-model="candidate.grade_level_id"
                                                            class="rounded-lg border-slate-200 p-2 text-[11px]" required>
                                                        <option value="">Year Level *</option>
                                                        @foreach($gradeLevels as $grade)
                                                            <option value="{{ $grade->id }}" :selected="String(candidate.grade_level_id) === '{{ $grade->id }}'">{{ $grade->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <select :name="`positions[${posIndex}][candidates][${candIndex}][section_id]`"
                                                            x-model="candidate.section_id"
                                                            class="rounded-lg border-slate-200 p-2 text-[11px]" required>
                                                        <option value="">Section *</option>
                                                        <template x-for="section in allSections.filter(s => String(s.grade_level_id) === String(candidate.grade_level_id))" :key="section.id">
                                                            <option :value="String(section.id)" :selected="String(section.id) === String(candidate.section_id)" x-text="section.name"></option>
                                                        </template>
                                                    </select>
                                                    <select :name="`positions[${posIndex}][candidates][${candIndex}][party]`"
                                                            x-model="candidate.party"
                                                            class="rounded-lg border-slate-200 p-2 text-[11px] shadow-sm">
                                                        <option value="Independent">Independent (Default)</option>
                                                        <template x-for="party in partylists" :key="party">
                                                            <option :value="party"
                                                                    :selected="candidate.party === party"
                                                                    x-text="party"></option>
                                                        </template>
                                                    </select>
                                                </div>

                                                <input type="hidden"
                                                       :name="`positions[${posIndex}][candidates][${candIndex}][drive_photo_url]`"
                                                       :value="candidate.drivePhotoUrl || ''">

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                    <div class="space-y-1">
                                                        <label class="text-[8px] font-black uppercase text-slate-400 ml-1">Profile / Bio *</label>
                                                        <textarea :name="`positions[${posIndex}][candidates][${candIndex}][bio]`"
                                                                  x-model="candidate.bio" rows="2"
                                                                  class="w-full rounded-xl border-slate-200 p-2 text-[11px] shadow-sm" required></textarea>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <label class="text-[8px] font-black uppercase text-slate-400 ml-1">Campaign Platform *</label>
                                                        <textarea :name="`positions[${posIndex}][candidates][${candIndex}][manifesto]`"
                                                                  x-model="candidate.manifesto" rows="2"
                                                                  class="w-full rounded-xl border-slate-200 p-2 text-[11px] shadow-sm" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="pos.candidates.length === 0"
                                     class="text-center py-6 text-[9px] font-black uppercase text-slate-300 tracking-widest">
                                    No candidates yet — click "+ Add Manually" to add.
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="formData.positions.length === 0"
                         class="text-center py-10 text-[9px] font-black uppercase text-slate-300 tracking-widest border-2 border-dashed border-slate-200 rounded-2xl">
                        No positions added yet. Go back to Step 3 to add positions or import CSV.
                    </div>

                </div>
            </div>

            <div class="bg-slate-50 px-8 py-6 border-t flex items-center justify-between">
                <button type="button" @click="if(currentStep > 1) currentStep--" :disabled="currentStep === 1"
                        class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-900 disabled:opacity-10 transition-all">
                    Previous
                </button>
                <div class="text-[9px] font-black text-slate-300 uppercase tracking-widest">
                    Phase <span x-text="currentStep"></span> of 4
                </div>
                <button type="button" x-show="currentStep < 4" @click="nextStep()"
                        class="px-8 py-3 bg-sky-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-sky-700 transition-all shadow-xl shadow-sky-100 active:scale-95">
                    Next Step
                </button>
                <button type="button" x-show="currentStep === 4" @click="launchElection()"
                        :disabled="isSubmitting"
                        class="px-10 py-3 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] hover:bg-black transition-all shadow-xl shadow-slate-200 active:scale-95 disabled:opacity-50"
                        x-text="isSubmitting ? 'Launching...' : 'Launch Election'">
                </button>
            </div>
        </form>
    </div>

    <script>
        const allSections = @json($sections);

        function electionForm() {
            return {
                currentStep: 1,
                isSubmitting: false,
                steps: [
                    { id: 1, title: 'Details' },
                    { id: 2, title: 'Eligibility' },
                    { id: 3, title: 'Positions' },
                    { id: 4, title: 'Candidates' }
                ],
                formData: {
                    title: '',
                    description: '',
                    start_at: '',
                    end_at: '',
                    eligibilityType: 'all',
                    positions: []
                },
                newPosition: { title: '', max_selection: 1, description: '' },
                partylists: [],
                csvStatus: '',
                csvHasError: false,
                showPartyModal: false,
                showReviewModal: false,
                showSuccessModal: false,
                partySaving: false,
                partyError: '',
                newParty: { name: '', description: '' },
                modalLogoFile: null,

                downloadTemplate() {
                    const headers = ['full name', 'position', 'year & section', 'party', 'short bio', 'platform', 'photo'];
                    const examples = [
                        ['Dela Cruz, Juan A.', 'SSG President', 'Grade 10 - Section A', 'Aksyon Party', 'Masipag at dedikadong lider.', 'Pagbutihin ang mga pasilidad.', ''],
                        ['Santos, Maria B.', 'SSG Vice President', 'Grade 10 - Section B', 'Independent', 'Aktibo sa mga gawain.', 'Palakasin ang ugnayan.', ''],
                    ];
                    const csvRows = [
                        headers.map(h => '"' + h + '"').join(','),
                        ...examples.map(row => row.map(v => '"' + v + '"').join(','))
                    ];
                    const blob = new Blob(['\uFEFF' + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'candidates_template.csv');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                },

                getDrivePreviewUrl(url) {
                    if (!url) return null;
                    const m1 = url.match(/[?&]id=([-\w]{25,})/);
                    const m2 = url.match(/\/d\/([-\w]{25,})/);
                    const fileId = m1 ? m1[1] : (m2 ? m2[1] : null);
                    if (fileId) return 'https://lh3.googleusercontent.com/d/' + fileId + '=w200';
                    return url;
                },

                // Flexible column matcher — kaya kahit anong Google Form question format
                resolveColumns(headers) {
                    // Mga keyword patterns para sa bawat field
                    const patterns = {
                        'full name':     [/full.?name/i, /pangalan/i, /kandidato/i, /name/i],
                        'position':      [/position.?applied/i, /position/i, /posisyon/i, /role/i, /tatakbuhan/i],
                        'grade':         [/grade.?level/i, /year.?level/i, /grade/i, /year/i, /taon/i],
                        'section':       [/section.{0,10}strand/i, /strand/i, /section/i, /klase/i],
                        'year & section':[/year.{0,5}section/i],
                        'party':         [/party/i, /partylist/i, /partido/i, /grupo/i],
                        'short bio':     [/short.?bio/i, /bio/i, /background/i, /profile/i, /about/i, /tungkol/i],
                        'platform':      [/platform/i, /manifesto/i, /agenda/i, /plano/i, /adhikain/i],
                        'photo':         [/photo/i, /picture/i, /larawan/i, /image/i, /pic/i, /drive/i, /link/i],
                    };

                    const map = {}; // field => actual header index
                    for (const [field, regexList] of Object.entries(patterns)) {
                        for (const regex of regexList) {
                            const idx = headers.findIndex(h => regex.test(h));
                            if (idx !== -1) { map[field] = idx; break; }
                        }
                    }
                    return map;
                },

                // Proper full CSV parser — kaya ng multi-line quoted fields (Google Form exports)
                parseCSVFull(text, delimiter = ',') {
                    const rows = [];
                    let current = [];
                    let field = '';
                    let inQuotes = false;
                    for (let i = 0; i < text.length; i++) {
                        const ch = text[i];
                        const next = text[i + 1];
                        if (inQuotes) {
                            if (ch === '"' && next === '"') { field += '"'; i++; } // escaped quote ""
                            else if (ch === '"') { inQuotes = false; }
                            else { field += ch; } // includes newlines inside quotes — OK lang!
                        } else {
                            if (ch === '"') { inQuotes = true; }
                            else if (ch === delimiter) { current.push(field.trim()); field = ''; }
                            else if (ch === '\n') { current.push(field.trim()); if (current.some(v => v)) rows.push(current); current = []; field = ''; }
                            else if (ch !== '\r') { field += ch; }
                        }
                    }
                    if (field || current.length) { current.push(field.trim()); if (current.some(v => v)) rows.push(current); }
                    return rows;
                },

                async importCSV(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.csvStatus = 'Reading file...';
                    this.csvHasError = false;
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        // Strip BOM character (invisible char ng Excel UTF-8 CSV)
                        let text = e.target.result.replace(/^\uFEFF/, '');

                        // Auto-detect delimiter — comma o semicolon
                        const firstLine = text.split(/\r?\n/)[0];
                        const delimiter = firstLine.includes(';') ? ';' : ',';

                        // Full CSV parse — handles multi-line quoted fields from Google Forms
                        const allRows = this.parseCSVFull(text, delimiter);
                        if (allRows.length < 2) { this.csvStatus = 'CSV is empty or has no data rows.'; this.csvHasError = true; return; }

                        const headers = allRows[0].map(h => h.replace(/^"|"$/g, '').trim());

                        // Flexible column matching
                        const colMap = this.resolveColumns(headers.map(h => h.toLowerCase()));

                        const missing = [];
                        if (colMap['full name'] === undefined) missing.push('full name');
                        if (colMap['position'] === undefined) missing.push('position');
                        if (missing.length > 0) {
                            this.csvStatus = 'Hindi mahanap ang columns: ' + missing.join(', ') + '. Mga header ng CSV: [' + headers.join(' | ') + ']';
                            this.csvHasError = true;
                            return;
                        }

                        const get = (row, field) => (colMap[field] !== undefined ? (row[colMap[field]] ?? '') : '').replace(/^"|"$/g, '').trim();

                        const toImport = [];
                        let skipped = 0;
                        for (let i = 1; i < allRows.length; i++) {
                            const row = allRows[i];
                            if (!row || row.every(v => !v)) continue;

                            // Split "SSG PRESIDENT — Description..." → title + description
                            const rawPosition = get(row, 'position');
                            const dashIdx = rawPosition.search(/\s*[—–]\s*/);
                            const positionTitle = (dashIdx !== -1 ? rawPosition.substring(0, dashIdx) : rawPosition).trim();
                            const positionDesc  = (dashIdx !== -1 ? rawPosition.substring(dashIdx).replace(/^\s*[—–]\s*/, '') : '').trim();

                            if (!positionTitle) { skipped++; continue; }
                            const fullName = get(row, 'full name');
                            if (!fullName) { skipped++; continue; }

                            const nameParts = this.parseName(fullName);
                            // Gamitin ang separate Grade Level + Section columns kung available
                            const gradeRaw   = get(row, 'grade');
                            const sectionRaw = get(row, 'section');
                            const yearSection = get(row, 'year & section');
                            const norm = str => str.toLowerCase().replace(/\s+/g, ' ').trim();

                            let gradeId = '', sectionId = '';

                            if (gradeRaw) {
                                const gradeMatch = gradeRaw.match(/(\d+)/);
                                const gradeNumber = gradeMatch ? gradeMatch[1] : null;
                                if (gradeNumber) {
                                    const found = allSections.find(s => {
                                        const gName = norm(s.grade_name || '');
                                        return gName === 'grade ' + gradeNumber || gName.includes(gradeNumber);
                                    });
                                    if (found) gradeId = String(found.grade_level_id);
                                }
                            }

                            if (sectionRaw) {
                                const parenMatch = sectionRaw.match(/^([^(]+)\(/);
                                const dashMatch  = sectionRaw.match(/[-–—]\s*([^(]+)/);
                                let rawSection = parenMatch ? norm(parenMatch[1]) : (dashMatch ? norm(dashMatch[1]) : norm(sectionRaw));
                                let found = allSections.find(s => norm(s.name) === rawSection && (!gradeId || String(s.grade_level_id) === gradeId));
                                if (!found) found = allSections.find(s => rawSection.includes(norm(s.name)) && (!gradeId || String(s.grade_level_id) === gradeId));
                                if (!found) found = allSections.find(s => norm(s.name).includes(rawSection) && (!gradeId || String(s.grade_level_id) === gradeId));
                                if (!found) found = allSections.find(s => norm(s.name) === rawSection);
                                if (found) { sectionId = String(found.id); if (!gradeId) gradeId = String(found.grade_level_id); }
                            } else if (yearSection) {
                                const parsed = this.parseYearSection(yearSection);
                                gradeId = parsed.gradeId; sectionId = parsed.sectionId;
                            }
                            const partyRaw = get(row, 'party');
                            const party = partyRaw || 'Independent';
                            if (party !== 'Independent' && !this.partylists.includes(party)) this.partylists.push(party);
                            const rawPhotoUrl = get(row, 'photo');
                            const drivePhotoUrl = rawPhotoUrl ? this.getDrivePreviewUrl(rawPhotoUrl) : null;

                            toImport.push({
                                positionTitle,
                                positionDesc,
                                candidate: {
                                    first_name: nameParts.first, middle_name: nameParts.middle, last_name: nameParts.last,
                                    // String conversion — PHP-rendered options ay strings, kaya dapat match
                                    grade_level_id: gradeId ? String(gradeId) : '', section_id: sectionId ? String(sectionId) : '', party,
                                    bio: get(row, 'short bio'), manifesto: get(row, 'platform'),
                                    photoPreview: null, drivePhotoUrl, fromCSV: true,
                                },
                            });
                        }

                        // Build positions locally muna bago i-assign sa Alpine
                        const positionsMap = {};
                        for (const item of toImport) {
                            const key = item.positionTitle.toLowerCase();
                            if (!positionsMap[key]) {
                                // Check kung may existing na position
                                const existing = this.formData.positions.find(p => p.title.toLowerCase() === key);
                                positionsMap[key] = existing
                                    ? { ...existing, candidates: [...(existing.candidates || [])] }
                                    : { title: item.positionTitle, max_selection: 1, description: item.positionDesc, candidates: [], fromCSV: true };
                            }
                            positionsMap[key].candidates.push(item.candidate);
                        }

                        // I-replace ang existing positions + dagdag ang bago
                        const existingKeys = this.formData.positions
                            .filter(p => !toImport.find(i => i.positionTitle.toLowerCase() === p.title.toLowerCase()))
                            .map(p => p);
                        // Force full reassignment para ma-trigger ang Alpine reactivity
                        this.formData.positions = [...existingKeys, ...Object.values(positionsMap)];

                        // DEBUG: I-log ang first candidate para makita ang values
                        if (this.formData.positions.length > 0 && this.formData.positions[0].candidates.length > 0) {
                            const c = this.formData.positions[0].candidates[0];
                            console.log('DEBUG CANDIDATE:', {
                                name: c.first_name + ' ' + c.last_name,
                                grade_level_id: c.grade_level_id,
                                section_id: c.section_id,
                                gradeType: typeof c.grade_level_id,
                                sectionType: typeof c.section_id
                            });
                        }
                        this.csvStatus = toImport.length + ' candidate(s) imported, ' + skipped + ' skipped.';
                        this.csvHasError = false;
                        event.target.value = '';
                    };
                    reader.readAsText(file);
                },

                parseCSVLine(line, delimiter = ',') {
                    const result = []; let current = ''; let inQuotes = false;
                    for (let i = 0; i < line.length; i++) {
                        const char = line[i];
                        if (char === '"') { inQuotes = !inQuotes; }
                        else if (char === delimiter && !inQuotes) { result.push(current); current = ''; }
                        else { current += char; }
                    }
                    result.push(current);
                    return result;
                },

                parseName(fullName) {
                    fullName = fullName.trim();
                    if (fullName.includes(',')) {
                        const firstComma = fullName.indexOf(',');
                        const last = fullName.substring(0, firstComma).trim();
                        const rest = fullName.substring(firstComma + 1).replace(/,/g, '').trim();
                        const parts = rest.split(/\s+/).filter(p => p);
                        const lastPart = parts[parts.length - 1] || '';
                        const isInitial = /^[A-Za-z]\.?$/.test(lastPart) && parts.length > 1;
                        if (isInitial) { return { first: parts.slice(0, parts.length - 1).join(' '), middle: lastPart, last }; }
                        return { first: parts[0] || '', middle: parts.slice(1).join(' ') || '', last };
                    }
                    const parts = fullName.split(/\s+/).filter(p => p);
                    if (parts.length === 1) return { first: parts[0], middle: '', last: '' };
                    if (parts.length === 2) return { first: parts[0], middle: '', last: parts[1] };
                    return { first: parts[0], middle: parts.slice(1, parts.length - 1).join(' '), last: parts[parts.length - 1] };
                },

                parseYearSection(yearSection) {
                    let gradeId = ''; let sectionId = '';
                    if (!yearSection) return { gradeId, sectionId };

                    const norm = str => str.toLowerCase().replace(/\s+/g, ' ').trim();

                    // Format 1: "ICT (Grade 11-12)" — section muna, tapos grade sa parenthesis
                    // Format 2: "Grade 12 - ICT" — grade muna, tapos section
                    // Format 3: "Grade 12 - Section A"

                    // Kunin ang grade number (kahit anong format)
                    const gradeMatch = yearSection.match(/(\d+)/);
                    const gradeNumber = gradeMatch ? gradeMatch[1] : null;
                    if (gradeNumber) {
                        const found = allSections.find(s => {
                            const gName = norm(s.grade_name || '');
                            return gName === 'grade ' + gradeNumber || gName.includes(gradeNumber);
                        });
                        if (found) gradeId = String(found.grade_level_id);
                    }

                    // Kunin ang section name:
                    // Format 1: "ICT (Grade 11-12)" → kuhanin ang part BAGO ng parenthesis
                    // Format 2: "Grade 12 - ICT" → kuhanin ang part PAGKATAPOS ng dash
                    let rawSection = '';

                    const parenMatch = yearSection.match(/^([^(]+)\(/);
                    const dashMatch  = yearSection.match(/[-–—]\s*([^(]+)/);

                    if (parenMatch) {
                        // "ICT (Grade 11-12)" → "ICT"
                        rawSection = norm(parenMatch[1]);
                    } else if (dashMatch) {
                        // "Grade 12 - ICT" → "ICT"
                        rawSection = norm(dashMatch[1]);
                    }

                    if (rawSection) {
                        // Exact match with grade filter
                        let found = allSections.find(s =>
                            norm(s.name) === rawSection &&
                            (!gradeId || String(s.grade_level_id) === String(gradeId))
                        );
                        // Partial: DB name inside CSV value
                        if (!found) found = allSections.find(s =>
                            rawSection.includes(norm(s.name)) &&
                            (!gradeId || String(s.grade_level_id) === String(gradeId))
                        );
                        // Partial: CSV value inside DB name
                        if (!found) found = allSections.find(s =>
                            norm(s.name).includes(rawSection) &&
                            (!gradeId || String(s.grade_level_id) === String(gradeId))
                        );
                        // Fallback: no grade filter
                        if (!found) found = allSections.find(s => norm(s.name) === rawSection);
                        if (!found) found = allSections.find(s => rawSection.includes(norm(s.name)));

                        if (found) {
                            sectionId = String(found.id);
                            if (!gradeId) gradeId = String(found.grade_level_id);
                        }
                    }

                    return { gradeId, sectionId };
                },

                previewModalLogo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.modalLogoFile = file;
                        const preview = document.getElementById('modal-logo-preview');
                        const placeholder = document.getElementById('modal-logo-placeholder');
                        const reader = new FileReader();
                        reader.onload = (e) => { preview.src = e.target.result; preview.classList.remove('hidden'); placeholder.classList.add('hidden'); };
                        reader.readAsDataURL(file);
                    }
                },

                async saveParty() {
                    this.partyError = '';
                    if (!this.newParty.name.trim()) { this.partyError = 'Party name is required.'; return; }
                    if (this.partylists.includes(this.newParty.name.trim())) { this.partyError = 'Party name already exists.'; return; }
                    this.partylists.push(this.newParty.name.trim());
                    this.newParty = { name: '', description: '' };
                    this.modalLogoFile = null;
                    document.getElementById('modal-logo-preview').classList.add('hidden');
                    document.getElementById('modal-logo-placeholder').classList.remove('hidden');
                    document.getElementById('modal-logo-input').value = '';
                    this.showPartyModal = false;
                },

                handleImagePreview(event, posIndex, candIndex) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.formData.positions[posIndex].candidates[candIndex].photoPreview = e.target.result;
                            this.formData.positions[posIndex].candidates[candIndex].drivePhotoUrl = null;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                nextStep() {
                    if (this.currentStep === 1 && (!this.formData.title || !this.formData.start_at || !this.formData.end_at)) return alert('Election details are required.');
                    if (this.currentStep === 3 && this.formData.positions.length === 0) return alert('At least one position is required.');
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                addPosition() {
                    if (!this.newPosition.title) return;
                    this.formData.positions.push({ title: this.newPosition.title, max_selection: this.newPosition.max_selection, description: this.newPosition.description, candidates: [], fromCSV: false });
                    this.newPosition.title = ''; this.newPosition.description = ''; this.newPosition.max_selection = 1;
                },

                removePosition(index) { this.formData.positions.splice(index, 1); },

                addCandidateToPosition(posIndex) {
                    this.formData.positions[posIndex].candidates.push({ first_name: '', middle_name: '', last_name: '', grade_level_id: '', section_id: '', party: 'Independent', bio: '', manifesto: '', photoPreview: null, drivePhotoUrl: null, fromCSV: false });
                },

                removeCandidate(posIndex, candIndex) { this.formData.positions[posIndex].candidates.splice(candIndex, 1); },

                launchElection() {
                    if (this.formData.positions.length === 0) return alert('Please add at least one position before launching.');

                    for (const pos of this.formData.positions) {
                        if (!pos.candidates || pos.candidates.length === 0) return alert(`Position "${pos.title}" has no candidates.`);
                        for (const cand of pos.candidates) {
                            if (!cand.first_name || !cand.last_name) return alert(`A candidate in "${pos.title}" is missing their name.`);
                            if (!cand.grade_level_id || !cand.section_id) return alert(`Candidate "${cand.first_name} ${cand.last_name}" is missing grade level or section.`);
                            if (!cand.bio) return alert(`Candidate "${cand.first_name} ${cand.last_name}" is missing their bio/profile.`);
                            if (!cand.manifesto) return alert(`Candidate "${cand.first_name} ${cand.last_name}" is missing their campaign platform.`);
                        }
                    }

                    this.showReviewModal = true;
                },

                async confirmLaunch() {
                    this.isSubmitting = true;

                    try {
                        const form = document.getElementById('electionForm');
                        const formData = new FormData(form);
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                            || document.querySelector('input[name="_token"]')?.value;

                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });

                        const text = await response.text();
                        let data = {};
                        try { data = JSON.parse(text); } catch(e) {}

                        if (response.ok && (data.success || response.status === 200)) {
                            this.showReviewModal = false;
                            this.showSuccessModal = true;
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong. Status: ' + response.status));
                        }
                    } catch (err) {
                        console.error('Fetch error:', err);
                        alert('Network error. Please try again.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>