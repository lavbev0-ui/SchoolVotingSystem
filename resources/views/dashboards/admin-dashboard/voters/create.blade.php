<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Add New Voter</h2>
                <p class="text-sm text-slate-500 italic">Register a new student account to the voting system.</p>
            </div>
            <a href="{{ route('admin.voters.index') }}" class="text-xs font-black text-sky-600 hover:text-sky-700 uppercase tracking-widest border-b-2 border-sky-100 hover:border-sky-600 transition-all">
                View Voter List →
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-[2.5rem] border border-slate-100 overflow-hidden">
            <div class="p-10">
                <form action="{{ route('admin.voters.store') }}" method="POST" enctype="multipart/form-data" id="createVoterForm">
                    @csrf
                    
                    <div class="space-y-8">

                        {{-- Photo & Student ID Section --}}
                        <div class="flex flex-col md:flex-row gap-8 items-center bg-slate-50/50 p-8 rounded-[2rem] border border-slate-100">
                            <div class="flex-shrink-0">
                                <div class="relative group">
                                    <div id="imagePreviewContainer" class="w-28 h-28 rounded-[2rem] bg-white flex items-center justify-center border-2 border-dashed border-slate-200 overflow-hidden relative shadow-inner group-hover:border-sky-400 transition-colors">
                                        <svg id="defaultUserIcon" xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-200 group-hover:text-sky-200 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <img id="imagePreview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                                        <input type="file" name="photo_path" id="photo_path" accept="image/*" onchange="handleImageUpload(this)" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                    </div>
                                    <button type="button" onclick="removeImage()" id="removeImageBtn" class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full p-1.5 shadow-lg hidden z-20 hover:bg-rose-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-3 text-center">Student Photo</p>
                                </div>
                                @error('photo_path') <p class="text-[9px] text-rose-500 font-bold mt-1 text-center uppercase tracking-tighter">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex-1 w-full grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Student ID *</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="student_id" id="student_id" value="{{ old('student_id') }}" class="block w-full rounded-2xl border-slate-200 p-3.5 text-sm focus:ring-sky-500 focus:border-sky-500 shadow-sm border @error('student_id') border-rose-500 @enderror" placeholder="e.g., 2026001" required>
                                        <button type="button" onclick="generateStudentId()" class="p-3.5 bg-white border border-slate-200 rounded-2xl hover:bg-sky-50 transition shadow-sm text-sky-600 active:scale-95">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                        </button>
                                    </div>
                                    @error('student_id') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                                </div>

                                {{-- ✅ Email OR Phone Number --}}
                                <div class="space-y-1.5" x-data="{ hasEmail: true }">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">
                                            <span x-show="hasEmail">Email Address</span>
                                            <span x-show="!hasEmail">Phone Number</span>
                                        </label>
                                        <button type="button" @click="hasEmail = !hasEmail"
                                            class="text-[9px] font-black uppercase tracking-widest text-sky-500 hover:text-sky-700 transition">
                                            <span x-show="hasEmail">Use Phone Instead →</span>
                                            <span x-show="!hasEmail">Use Email Instead →</span>
                                        </button>
                                    </div>

                                    {{-- Email Field --}}
                                    <div x-show="hasEmail">
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="block w-full rounded-2xl border-slate-200 p-3.5 text-sm focus:ring-sky-500 shadow-sm border @error('email') border-rose-500 @enderror"
                                            placeholder="student@example.com">
                                        @error('email') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- Phone Field --}}
                                    <div x-show="!hasEmail">
                                        <div class="flex">
                                            <span class="inline-flex items-center px-4 rounded-l-2xl border border-r-0 border-slate-200 bg-slate-50 text-slate-500 text-sm font-bold">🇵🇭 +63</span>
                                            <input type="tel" name="phone_number" value="{{ old('phone_number') }}"
                                                class="block w-full rounded-r-2xl border-slate-200 p-3.5 text-sm focus:ring-sky-500 shadow-sm border @error('phone_number') border-rose-500 @enderror"
                                                placeholder="9XXXXXXXXX" maxlength="10">
                                        </div>
                                        <p class="text-[9px] text-slate-400 font-bold mt-1 ml-1 uppercase tracking-widest">
                                            2FA code will be sent via SMS
                                        </p>
                                        @error('phone_number') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Name Section --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">First Name *</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="block w-full rounded-2xl border-slate-200 p-3.5 text-sm shadow-sm border focus:ring-sky-500 @error('first_name') border-rose-500 @enderror" placeholder="Juan" required>
                                @error('first_name') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="block w-full rounded-2xl border-slate-200 p-3.5 text-sm shadow-sm border focus:ring-sky-500" placeholder="Santos">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Last Name *</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="block w-full rounded-2xl border-slate-200 p-3.5 text-sm shadow-sm border focus:ring-sky-500 @error('last_name') border-rose-500 @enderror" placeholder="Dela Cruz" required>
                                @error('last_name') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Grade & Section --}}
                        <div x-data="{ gradeLevel: '{{ old('grade_level_id') }}', sections: {{ Js::from($sections) }} }" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Grade Level *</label>
                                <select name="grade_level_id" x-model="gradeLevel" required class="w-full rounded-2xl border-slate-200 p-3.5 text-sm shadow-sm border focus:ring-sky-500">
                                    <option value="">Select Grade Level</option>
                                    @foreach ($gradeLevels as $grade)
                                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endforeach
                                </select>
                                @error('grade_level_id') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Section / Strand *</label>
                                <select name="section_id" :disabled="!gradeLevel" required class="w-full rounded-2xl border-slate-200 p-3.5 text-sm shadow-sm border focus:ring-sky-500 disabled:bg-slate-50 disabled:text-slate-300">
                                    <option value="">Select Section</option>
                                    <template x-for="s in sections.filter(sec => sec.grade_level_id == gradeLevel)" :key="s.id">
                                        <option :value="s.id" x-text="s.name" :selected="s.id == '{{ old('section_id') }}'"></option>
                                    </template>
                                </select>
                                @error('section_id') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Password Section --}}
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Security Password *</label>
                            <div class="flex flex-col md:flex-row gap-3">
                                <div class="flex-1 relative">
                                    <input type="password" name="password" id="password" class="block w-full rounded-2xl border-slate-200 p-3.5 text-sm shadow-sm border focus:ring-sky-500 pr-12" placeholder="••••••••" required minlength="8">
                                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 px-4 flex items-center text-slate-300 hover:text-sky-500 transition-colors">
                                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <button type="button" onclick="generatePassword()" class="px-6 py-3.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-sky-600 transition-all shadow-lg active:scale-95">Auto-Generate</button>
                            </div>
                            @error('password') <p class="text-[9px] text-rose-500 font-bold ml-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-4 pt-10 mt-10 border-t border-slate-50">
                        <a href="{{ route('admin.voters.index') }}" class="px-6 py-3 font-black uppercase text-[10px] tracking-widest text-slate-400 hover:text-slate-600 transition">Cancel</a>
                        <button type="submit" class="px-12 py-4 bg-sky-600 text-white rounded-[1.5rem] font-black uppercase text-xs tracking-widest shadow-xl shadow-sky-100 hover:bg-sky-700 hover:-translate-y-0.5 transition-all">Register Voter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generateStudentId() {
            const year = new Date().getFullYear();
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            document.getElementById('student_id').value = `${year}${randomNum}`;
        }

        function generatePassword() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
            let password = '';
            for (let i = 0; i < 10; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const input = document.getElementById('password');
            input.value = password;
            input.type = 'text';
            document.getElementById('eyeIcon').classList.add('hidden');
            document.getElementById('eyeOffIcon').classList.remove('hidden');
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const eye = document.getElementById('eyeIcon');
            const eyeOff = document.getElementById('eyeOffIcon');
            if (input.type === 'password') {
                input.type = 'text'; eye.classList.add('hidden'); eyeOff.classList.remove('hidden');
            } else {
                input.type = 'password'; eye.classList.remove('hidden'); eyeOff.classList.add('hidden');
            }
        }

        function handleImageUpload(input) {
            const file = input.files[0];
            const icon = document.getElementById('defaultUserIcon');
            const preview = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImageBtn');
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    icon.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            const input = document.getElementById('photo_path');
            const icon = document.getElementById('defaultUserIcon');
            const preview = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImageBtn');
            input.value = ''; preview.src = '#'; preview.classList.add('hidden');
            icon.classList.remove('hidden'); removeBtn.classList.add('hidden');
        }
    </script>
</x-app-layout>