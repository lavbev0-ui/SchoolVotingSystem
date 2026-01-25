<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Add New Voter</h2>
            <p class="text-sm text-gray-500">Create a new student account to allow them to vote in elections</p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
            <div class="p-6">
                
                <form action="{{ route('dashboard.voters.store') }}" method="POST" enctype="multipart/form-data" id="createVoterForm">
                    @csrf

                    <div class="space-y-6">

                        {{-- Student ID Section --}}
                        <div class="space-y-2">
                            <label for="userID" class="block text-sm font-medium text-gray-700">
                                Student ID <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input 
                                        type="text" 
                                        name="userID" 
                                        id="userID" 
                                        value="{{ old('userID') }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('userID') border-red-500 @enderror" 
                                        placeholder="e.g., 2026001"
                                        required
                                    >
                                    @error('userID')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="button" onclick="generateStudentId()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Generate
                                </button>
                            </div>
                        </div>

                        {{-- Name Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- First Name --}}
                            <div class="space-y-2">
                                <label for="first_name" class="block text-sm font-medium text-gray-700">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="first_name" 
                                    id="first_name" 
                                    value="{{ old('first_name') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('first_name') border-red-500 @enderror" 
                                    placeholder="Juan"
                                    required
                                >
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Middle Name --}}
                            <div class="space-y-2">
                                <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                                <input 
                                    type="text" 
                                    name="middle_name" 
                                    id="middle_name" 
                                    value="{{ old('middle_name') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                    placeholder="Santos"
                                >
                            </div>

                            {{-- Last Name --}}
                            <div class="space-y-2">
                                <label for="last_name" class="block text-sm font-medium text-gray-700">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="last_name" 
                                    id="last_name" 
                                    value="{{ old('last_name') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('last_name') border-red-500 @enderror" 
                                    placeholder="Dela Cruz"
                                    required
                                >
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Grade & Section Grid --}}
                        <div 
                            x-data="{ 
                                grade: '{{ old('grade_level_id') }}', 
                                section: '{{ old('section_id') }}', 
                                allSections: {{ Js::from($sections) }} 
                            }" 
                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                        >

                            {{-- Grade Level Select --}}
                            <div class="space-y-2">
                                <label for="grade_level_id" class="block text-sm font-medium text-gray-700">
                                    Grade Level <span class="text-red-500">*</span>
                                </label>
                                
                                <select 
                                    name="grade_level_id" 
                                    id="grade_level_id" 
                                    x-model="grade"
                                    @change="section = ''" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                >
                                    <option value="">Select Grade</option>
                                    @foreach($gradeLevels as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                                
                                @error('grade_level_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Section Select (Dynamic) --}}
                            <div class="space-y-2">
                                <label for="section_id" class="block text-sm font-medium text-gray-700">
                                    Section <span class="text-red-500">*</span>
                                </label>
                                
                                <select 
                                    name="section_id" 
                                    id="section_id" 
                                    x-model="section"
                                    :disabled="!grade"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:bg-gray-100 disabled:text-gray-400"
                                    required
                                >
                                    <option value="">Select Section</option>
                                    
                                    {{-- Filter sections based on the selected grade ID --}}
                                    <template x-for="s in allSections.filter(i => i.grade_level_id == grade)" :key="s.id">
                                        <option :value="s.id" x-text="s.name"></option>
                                    </template>
                                    
                                    {{-- Message if no sections exist for that grade --}}
                                    <template x-if="grade && allSections.filter(i => i.grade_level_id == grade).length === 0">
                                        <option disabled>No sections found</option>
                                    </template>
                                </select>

                                @error('section_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email (Optional)</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                placeholder="student@example.com"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Section --}}
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10 @error('password') border-red-500 @enderror" 
                                        placeholder="Enter password"
                                        required
                                        minlength="6"
                                    >
                                    {{-- Toggle Visibility Button --}}
                                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <button type="button" onclick="generatePassword()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Generate
                                </button>
                            </div>
                            <p class="text-xs text-gray-500">Minimum 6 characters. Student will use this password to log in.</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Profile Image --}}
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">Profile Image (Optional)</label>
                            
                            <div class="flex items-start gap-4">
                                {{-- Preview Area --}}
                                <div class="flex-shrink-0">
                                    <div id="imagePreviewContainer" class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center border-2 border-dashed border-blue-300 overflow-hidden relative group">
                                        {{-- Default Icon --}}
                                        <svg id="defaultUserIcon" xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{-- Actual Image Preview (Hidden by default) --}}
                                        <img id="imagePreview" src="#" alt="Profile Preview" class="w-full h-full object-cover hidden">
                                        
                                        {{-- Remove Button --}}
                                        <button type="button" onclick="removeImage()" id="removeImageBtn" class="absolute inset-0 bg-black bg-opacity-50 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hidden">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Upload Input --}}
                                <div class="flex-1">
                                    <div class="relative group">
                                        <input 
                                            type="file" 
                                            name="photo_path" 
                                            id="photo_path" 
                                            accept="image/*"
                                            onchange="handleImageUpload(this)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        >
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Click to upload profile image</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">PNG, JPG up to 5MB</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p id="uploadSuccessMsg" class="text-xs text-green-600 mt-2 items-center gap-1 hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Image selected successfully
                                    </p>
                                    @error('photo_path')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Info Alert --}}
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong>Note:</strong> Make sure to securely share the Student ID and Password with the student. They will need both to access the voting system.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <a href="{{ route('dashboard.voters.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add Voter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script for Interactivity --}}
    <script>
        // 1. Generate Student ID
        function generateStudentId() {
            const year = new Date().getFullYear();
            const randomNum = Math.floor(1000 + Math.random() * 9000);
            document.getElementById('userID').value = `${year}${randomNum}`;
        }

        // 2. Generate Password
        function generatePassword() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
            let password = '';
            for (let i = 0; i < 8; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const passwordInput = document.getElementById('password');
            passwordInput.value = password;
            
            // If currently hidden, show it briefly so user sees the new password? 
            // Or keep logic simple (user can toggle visibility manually)
            if(passwordInput.type === 'password') {
                togglePasswordVisibility();
            }
        }

        // 3. Toggle Password Visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        // 4. Image Preview Logic
        function handleImageUpload(input) {
            const file = input.files[0];
            const defaultIcon = document.getElementById('defaultUserIcon');
            const previewImg = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImageBtn');
            const successMsg = document.getElementById('uploadSuccessMsg');

            if (file) {
                // Size validation (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB');
                    input.value = ''; // Reset input
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                    defaultIcon.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                    successMsg.classList.remove('hidden');
                    successMsg.classList.add('flex');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            const input = document.getElementById('photo_path');
            const defaultIcon = document.getElementById('defaultUserIcon');
            const previewImg = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImageBtn');
            const successMsg = document.getElementById('uploadSuccessMsg');

            input.value = ''; // Clear file input
            previewImg.src = '#';
            previewImg.classList.add('hidden');
            defaultIcon.classList.remove('hidden');
            removeBtn.classList.add('hidden');
            successMsg.classList.add('hidden');
            successMsg.classList.remove('flex');
        }
    </script>
</x-app-layout>