<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Edit Voter</h2>
            <p class="text-sm text-gray-500">Update student account details for: <strong>{{ $voter->full_name }}</strong></p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            <div class="p-6">
                
                <form action="{{ route('admin.voters.update', $voter->id) }}" method="POST" enctype="multipart/form-data" id="editVoterForm">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">

                        {{-- Student ID Section --}}
                        <div class="space-y-2">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">
                                Student ID <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input 
                                        type="text" 
                                        name="student_id" 
                                        id="student_id" 
                                        value="{{ old('student_id', $voter->student_id) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('student_id') border-red-500 @enderror" 
                                        required
                                    >
                                </div>
                                <button type="button" onclick="generateStudentID()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Generate
                                </button>
                            </div>
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Name Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $voter->first_name) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="space-y-2">
                                <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $voter->middle_name) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="space-y-2">
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $voter->last_name) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                        </div>

                        {{-- ✅ Email & Phone Number --}}
                        <div x-data="{ hasEmail: {{ !empty($voter->email) ? 'true' : (!empty($voter->phone_number) ? 'false' : 'true') }} }" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- Email --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $voter->email) }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-500 @enderror"
                                    placeholder="student@example.com">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- ✅ Phone Number --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Phone Number
                                    <span class="text-xs text-indigo-500 font-normal ml-1">(para sa SMS 2FA kung walang email)</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-bold">🇵🇭 +63</span>
                                    <input type="tel" name="phone_number" 
                                        value="{{ old('phone_number', ltrim($voter->phone_number ?? '', '+630')) }}"
                                        class="block w-full rounded-r-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone_number') border-red-500 @enderror"
                                        placeholder="9XXXXXXXXX" maxlength="10">
                                </div>
                                <p class="text-xs text-gray-400">Halimbawa: 9171234567</p>
                                @error('phone_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Grade & Section Grid --}}
                        <div x-data="{ 
                                grade: '{{ old('grade_level_id', $voter->grade_level_id) }}', 
                                section: '{{ old('section_id', $voter->section_id) }}', 
                                allSections: {{ Js::from($sections) }} 
                            }" 
                            class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div class="space-y-2">
                                <label for="grade_level_id" class="block text-sm font-medium text-gray-700">Grade Level *</label>
                                <select name="grade_level_id" id="grade_level_id" x-model="grade" @change="section = ''" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @foreach($gradeLevels as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="section_id" class="block text-sm font-medium text-gray-700">Section *</label>
                                <select name="section_id" id="section_id" x-model="section" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <template x-for="s in allSections.filter(i => i.grade_level_id == grade)" :key="s.id">
                                        <option :value="s.id" x-text="s.name" :selected="s.id == section"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        {{-- Profile Photo --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden border">
                                    @if($voter->photo_path)
                                        <img src="{{ asset('storage/' . $voter->photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-300">
                                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="photo_path" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                        </div>

                        {{-- Account Status --}}
                        <div class="space-y-2">
                            <label for="is_active" class="block text-sm font-medium text-gray-700">Account Status</label>
                            <select name="is_active" id="is_active" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="1" {{ old('is_active', $voter->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $voter->is_active) == 0 ? 'selected' : '' }}>Disabled</option>
                            </select>
                        </div>

                        {{-- Password --}}
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2 border border-gray-100">
                            <label for="password" class="block text-sm font-medium text-indigo-700">Change Password <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="password" name="password" id="password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10" placeholder="Leave blank to keep current">
                                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-indigo-500">
                                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                </div>
                                <button type="button" onclick="generatePassword()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Generate</button>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('admin.voters.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-10 py-2 border border-transparent text-sm font-bold rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 uppercase tracking-widest">Update Voter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generateStudentID() {
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
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-app-layout>