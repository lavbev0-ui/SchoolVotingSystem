<x-guest-layout>
    <div class="w-full max-w-md">

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-300">

            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-8 h-8 text-blue-600"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-6 2.17-6 4v1h12v-1c0-1.83-2.67-4-6-4z" />
                    </svg>
                </div>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Student Login</h1>
                <p class="text-sm text-gray-600">Enter your credentials to access the voting system</p>
            </div>

            <form method="POST" action="{{ route('voter.login') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                    <input
                        id="student_id"
                        name="student_id"
                        type="text"
                        value="{{ old('student_id') }}"
                        required
                        autofocus
                        placeholder="Enter your student ID"
                        class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                    >
                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 pr-12"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password', 'eyeIcon1')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-blue-600"
                            tabindex="-1">
                            <svg id="eyeIcon1" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                                         a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                                         M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59
                                         m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                                         m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7
                                         a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <input
                        id="remember_me"
                        name="remember"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                    >
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                {{-- Terms and Conditions Checkbox --}}
                <div class="flex items-start gap-2">
                    <input
                        id="terms"
                        type="checkbox"
                        required
                        class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                    >
                    <label for="terms" class="text-sm text-gray-600">
                        I agree to the
                        <button type="button" onclick="document.getElementById('termsModal').classList.remove('hidden')"
                                class="text-blue-600 hover:underline font-semibold">
                            Terms and Conditions
                        </button>
                        of the Enhance Voting System.
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full h-12 bg-blue-700 text-white text-base font-semibold rounded-lg hover:bg-blue-800 transition shadow-md active:scale-95">
                    Login
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500 font-medium">
                <p>Forgot your password? Contact your school administrator.</p>
            </div>
        </div>
    </div>

    {{-- TERMS AND CONDITIONS MODAL --}}
    <div id="termsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-5 border-b border-gray-100">
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-900">Terms and Conditions</h2>
                <button onclick="document.getElementById('termsModal').classList.add('hidden')"
                        class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="overflow-y-auto flex-1 px-8 py-6 space-y-4 text-sm text-gray-600 leading-relaxed">

                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">
                    Catalino D. Cerezo National High School — Enhance Voting System
                </p>

                <p>By logging in and using this voting system, you agree to the following terms and conditions:</p>

                <div class="space-y-3">
                    <div>
                        <p class="font-bold text-gray-800">1. Eligibility</p>
                        <p>Only registered students of Catalino D. Cerezo National High School are authorized to use this system. Unauthorized access is strictly prohibited.</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-800">2. One Vote Per Student</p>
                        <p>Each student is entitled to cast only one ballot per election. Attempting to vote multiple times is a violation of the election rules.</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-800">3. Confidentiality</p>
                        <p>Your vote is confidential. Do not share your Student ID and password with anyone. You are responsible for all activities performed under your account.</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-800">4. Honest Participation</p>
                        <p>Any form of electoral fraud, vote buying, coercion, or manipulation is strictly prohibited and will be subject to disciplinary action.</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-800">5. Password Security</p>
                        <p>You may only change your password once. If you forget your password, contact your school administrator for assistance.</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-800">6. Data Privacy</p>
                        <p>Your personal information is collected solely for election purposes and will be kept confidential in accordance with the Data Privacy Act of 2012 (RA 10173).</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-800">7. System Integrity</p>
                        <p>Any attempt to tamper with, hack, or manipulate the system will be reported to school authorities and may result in serious consequences.</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-800">8. Acceptance</p>
                        <p>By checking the agreement box and logging in, you confirm that you have read, understood, and agreed to these Terms and Conditions.</p>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-8 py-4 border-t border-gray-100">
                <button onclick="
                    document.getElementById('terms').checked = true;
                    document.getElementById('termsModal').classList.add('hidden');
                "
                    class="w-full py-3 bg-blue-700 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-800 transition-all">
                    I Agree & Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                             -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                             a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                             M9.878 9.878l4.242 4.242M9.88 9.88L6.59 6.59
                             m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                             m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7
                             a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            }
        }
    </script>
</x-guest-layout>