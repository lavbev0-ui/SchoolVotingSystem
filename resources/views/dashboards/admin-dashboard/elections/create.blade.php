<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8"
         x-data="electionForm()"
         x-cloak>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create New Election</h1>
            <p class="mt-2 text-sm text-gray-600">
                Setup the election details, eligibility, positions, and candidates.
            </p>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between w-full">
                <template x-for="(stepObj, index) in steps" :key="index">
                    <div class="flex items-center flex-1 relative">
                        <div class="flex flex-col items-center flex-1 z-10">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 transition-colors border-2"
                                 :class="{
                                     'bg-indigo-600 border-indigo-600 text-white': currentStep > stepObj.id,
                                     'bg-white border-indigo-600 text-indigo-600': currentStep === stepObj.id,
                                     'bg-gray-100 border-gray-300 text-gray-500': currentStep < stepObj.id
                                 }">
                                <span x-text="stepObj.id" class="font-bold"></span>
                            </div>
                            <p class="text-xs font-medium text-center uppercase tracking-wider"
                               :class="currentStep >= stepObj.id ? 'text-indigo-600' : 'text-gray-500'"
                               x-text="stepObj.title"></p>
                        </div>
                        <div x-show="index < steps.length - 1"
                             class="absolute top-5 left-1/2 w-full h-0.5 -z-0"
                             :class="currentStep > stepObj.id ? 'bg-indigo-600' : 'bg-gray-200'">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <form action="{{ route('dashboard.elections.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg border border-gray-200">
            @csrf

            <div class="p-6 sm:p-8 min-h-[400px]">

                <div x-show="currentStep === 1" class="space-y-6 transition-all duration-300">
                    <div class="space-y-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Election Title *</label>
                        <input type="text" name="title" id="title" required
                               x-model="formData.title"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                               placeholder="e.g., Supreme Student Government Election 2026">
                    </div>

                    <div class="space-y-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700">Description / Bio</label>
                        <textarea name="bio" id="bio" rows="4"
                                  x-model="formData.bio"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                                  placeholder="Describe the purpose and details of this election..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="start_at" class="block text-sm font-medium text-gray-700">Start Date & Time *</label>
                            <input type="datetime-local" name="start_at" id="start_at" required
                                   x-model="formData.start_at"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                        </div>

                        <div class="space-y-2">
                            <label for="end_at" class="block text-sm font-medium text-gray-700">End Date & Time *</label>
                            <input type="datetime-local" name="end_at" id="end_at" required
                                   x-model="formData.end_at"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                        </div>
                    </div>
                </div>

                <div x-show="currentStep === 2" class="space-y-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Who can vote in this election? *</h3>

                    <input type="hidden" name="eligibility_type" :value="formData.eligibilityType">

                    <div class="grid grid-cols-1 gap-4">
                        <label class="relative flex items-start p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition-all"
                               :class="formData.eligibilityType === 'all' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                            <div class="flex items-center h-5">
                                <input type="radio" name="eligibility_radio" value="all" x-model="formData.eligibilityType" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <span class="font-medium text-gray-900">All Students</span>
                                <p class="text-gray-500">All registered students can vote.</p>
                            </div>
                        </label>

                        <label class="relative flex flex-col p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition-all"
                               :class="formData.eligibilityType === 'grade_level' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="eligibility_radio" value="grade_level" x-model="formData.eligibilityType" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-medium text-gray-900">Specific Grade Levels</span>
                                    <p class="text-gray-500">Only students from selected grades.</p>
                                </div>
                            </div>

                            <div x-show="formData.eligibilityType === 'grade_level'" class="mt-4 ml-8 grid grid-cols-2 gap-2 transition-all">
                                @foreach($gradeLevels as $grade)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox"
                                            name="selected_grades[]"
                                            value="{{ $grade->id }}"
                                            class="rounded border-gray-300 text-indigo-600">
                                        <span class="ml-2 text-sm text-gray-600">{{ $grade->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </label>

                        <label class="relative flex flex-col p-4 border rounded-lg hover:bg-gray-50 cursor-pointer transition-all"
                               :class="formData.eligibilityType === 'section' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="radio" name="eligibility_radio" value="section" x-model="formData.eligibilityType" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <span class="font-medium text-gray-900">Specific Sections</span>
                                    <p class="text-gray-500">Only students from selected sections.</p>
                                </div>
                            </div>

                            <div x-show="formData.eligibilityType === 'section'" class="mt-4 ml-8 grid grid-cols-3 gap-2 transition-all">
                                @foreach($sections as $section)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox"
                                            name="selected_sections[]"
                                            value="{{ $section->id }}"
                                            class="rounded border-gray-300 text-indigo-600">
                                        <span class="ml-2 text-sm text-gray-600">
                                            {{ $section->name }}
                                            <span class="text-xs text-gray-400">
                                                ({{ $section->gradeLevel->name }})
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </label>
                    </div>
                </div>

                <div x-show="currentStep === 3" class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Add Positions</h3>
                        <p class="text-sm text-gray-500">Define the positions students will vote for.</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Position Name</label>
                            <input type="text" x-model="newPosition.title"
                                placeholder="e.g., President"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea x-model="newPosition.description"
                                    placeholder="Describe this position's role..."
                                    rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Max Selections</label>
                            <select x-model="newPosition.max_selection"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                <option value="1">1 (Single winner)</option>
                                <option value="2">2 winners</option>
                                <option value="3">3 winners</option>
                                <option value="4">4 winners</option>
                                <option value="5">5 winners</option>
                            </select>
                        </div>

                        <button type="button" @click="addPosition()"
                                class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="mr-2">+</span> Add Position
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(pos, index) in formData.positions" :key="index">
                            <div class="bg-white p-4 border rounded-lg flex justify-between items-start shadow-sm transition-all hover:shadow-md">
                                <div>
                                    <input type="hidden" :name="`positions[${index}][title]`" :value="pos.title">
                                    <input type="hidden" :name="`positions[${index}][description]`" :value="pos.description">
                                    <input type="hidden" :name="`positions[${index}][max_selection]`" :value="pos.max_selection">
                                    <input type="hidden" :name="`positions[${index}][order]`" :value="index">

                                    <h4 class="font-bold text-gray-900" x-text="pos.title"></h4>
                                    <p class="text-sm text-gray-500 mt-1" x-text="pos.description"></p>

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        Max <span x-text="pos.max_selection" class="mx-1"></span> Winner(s)
                                    </span>
                                </div>
                                <button type="button" @click="removePosition(index)"
                                        class="text-red-600 hover:text-red-900 text-sm font-medium">
                                    Remove
                                </button>
                            </div>
                        </template>

                        <div x-show="formData.positions.length === 0"
                            class="text-center py-6 text-gray-400 border-2 border-dashed rounded-lg bg-gray-50">
                            No positions added yet.
                        </div>
                    </div>
                </div>

                <div x-show="currentStep === 4" class="space-y-8">
                    <div class="space-y-2">
                        <h3 class="text-lg font-medium text-gray-900">Add Candidates</h3>
                        <p class="text-sm text-gray-500">Add candidates for each position defined in the previous step.</p>
                    </div>

                    <template x-for="(pos, posIndex) in formData.positions" :key="posIndex">
                        <div class="bg-white border rounded-lg shadow-sm overflow-hidden mb-6">
                            <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                <h4 class="font-bold text-gray-800 text-lg" x-text="pos.title"></h4>
                                <span class="text-xs text-gray-500 uppercase tracking-wide">Position <span x-text="posIndex + 1"></span></span>
                            </div>

                            <div class="p-4 space-y-6">
                                <template x-for="(candidate, candIndex) in pos.candidates" :key="candIndex">
                                    <div class="relative border rounded-md p-4 bg-gray-50 hover:shadow-md transition-shadow">

                                        <button type="button" @click="removeCandidate(posIndex, candIndex)"
                                                class="absolute top-2 right-2 text-gray-400 hover:text-red-600 transition-colors z-10">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>

                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                            <div class="md:col-span-3 text-center">
                                                <label class="block text-xs font-medium text-gray-700 mb-2">Photo</label>
                                                <div class="relative w-full h-32 border-2 border-dashed border-gray-300 rounded-lg flex flex-col justify-center items-center bg-white hover:bg-gray-50 transition overflow-hidden">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span class="text-xs text-gray-500 mt-1">Select File</span>
                                                    <input type="file"
                                                           :name="`positions[${posIndex}][candidates][${candIndex}][photo]`"
                                                           accept="image/*"
                                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                                </div>
                                            </div>

                                            <div class="md:col-span-9 space-y-3">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700">Full Name *</label>
                                                        <input type="text" required
                                                               x-model="candidate.name"
                                                               :name="`positions[${posIndex}][candidates][${candIndex}][name]`"
                                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700">Party / Affiliation</label>
                                                        <input type="text"
                                                               x-model="candidate.party"
                                                               :name="`positions[${posIndex}][candidates][${candIndex}][party]`"
                                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700">Grade Level</label>
                                                        <select 
                                                            :name="`positions[${posIndex}][candidates][${candIndex}][grade_level_id]`"
                                                            x-model="candidate.grade_level_id"
                                                            @change="candidate.section_id = ''" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                                                            
                                                            <option value="">Select Grade</option>
                                                            @foreach($gradeLevels ?? [] as $g)
                                                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label class="block text-xs font-medium text-gray-700">Section</label>
                                                        <select 
                                                            :name="`positions[${posIndex}][candidates][${candIndex}][section_id]`"
                                                            x-model="candidate.section_id"
                                                            :disabled="!candidate.grade_level_id"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border disabled:bg-gray-100 disabled:text-gray-400">
                                                            
                                                            <option value="">Select Section</option>
                                                            
                                                            <template x-for="section in allSections.filter(s => s.grade_level_id == candidate.grade_level_id)" :key="section.id">
                                                                <option :value="section.id" x-text="section.name"></option>
                                                            </template>
                                                            
                                                            <template x-if="candidate.grade_level_id && allSections.filter(s => s.grade_level_id == candidate.grade_level_id).length === 0">
                                                                <option disabled>No sections found</option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700">Biography</label>
                                                    <textarea 
                                                        :name="`positions[${posIndex}][candidates][${candIndex}][bio]`"
                                                        x-model="candidate.bio"
                                                        rows="2"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border"
                                                        placeholder="Short description of the candidate..."
                                                    ></textarea>
                                                </div>

                                                <div class="mt-3">
                                                    <label class="block text-xs font-medium text-gray-700">Platform / Manifesto</label>
                                                    <textarea 
                                                        :name="`positions[${posIndex}][candidates][${candIndex}][platform]`"
                                                        x-model="candidate.platform" 
                                                        rows="2"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border"
                                                        placeholder="Goals and promises..."
                                                    ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button type="button"
                                        @click="addCandidateToPosition(posIndex)"
                                        class="w-full flex justify-center items-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:border-indigo-500 hover:text-indigo-600 transition-all">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Candidate for <span x-text="pos.title" class="ml-1 font-bold"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between rounded-b-lg">
                <button type="button"
                        @click="if(currentStep > 1) currentStep--"
                        :disabled="currentStep === 1"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>

                <div class="text-sm text-gray-500">
                    Step <span x-text="currentStep"></span> of 4
                </div>

                <button type="button"
                        x-show="currentStep < 4"
                        @click="nextStep()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Next Step
                </button>

                <button type="submit"
                        x-show="currentStep === 4"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Create Election
                </button>
            </div>
        </form>
    </div>

    <script>
        const allSections = @json($sections);

        function electionForm() {
            return {
                currentStep: 1,
                steps: [
                    { id: 1, title: 'Details' },
                    { id: 2, title: 'Eligibility' },
                    { id: 3, title: 'Positions' },
                    { id: 4, title: 'Candidates' }
                ],
                formData: {
                    title: '',
                    bio: '',
                    start_at: '',
                    end_at: '',
                    eligibilityType: 'all',
                    positions: []
                },
                newPosition: {
                    title: '',
                    max_selection: 1
                },

                validateDates() {
                    const start = this.formData.start_at;
                    const end = this.formData.end_at;
                    if (start && end && new Date(end) <= new Date(start)) {
                        alert('End Date must be later than the Start Date.');
                        return false;
                    }
                    return true;
                },

                nextStep() {
                    if (this.currentStep === 1) {
                        if (!this.formData.title || !this.formData.start_at || !this.formData.end_at) {
                            alert('Please fill in all required fields.');
                            return;
                        }
                        if (!this.validateDates()) return;
                    }

                    if (this.currentStep === 3) {
                        if (this.formData.positions.length === 0) {
                            alert('Please add at least one position.');
                            return;
                        }
                    }

                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                addPosition() {
                    if (!this.newPosition.title) return;

                    this.formData.positions.push({
                        title: this.newPosition.title,
                        description: this.newPosition.description,
                        max_selection: this.newPosition.max_selection,
                        candidates: []
                    });

                    this.newPosition.title = '';
                    this.newPosition.description = '';
                    this.newPosition.max_selection = 1;
                },

                removePosition(index) {
                    this.formData.positions.splice(index, 1);
                },

                addCandidateToPosition(posIndex) {
                    this.formData.positions[posIndex].candidates.push({
                        name: '',
                        grade_level_id: '',
                        section_id: '',
                        bio: '',
                        party: '',
                        photo: null
                    });
                },

                removeCandidate(posIndex, candIndex) {
                    this.formData.positions[posIndex].candidates.splice(candIndex, 1);
                }
            }
        }
    </script>
</x-app-layout>