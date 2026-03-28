<?php

use App\Livewire\Forms\LoginForm;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;
    public bool $agreedToTerms = false;
    public string $errorMessage = '';

    public function login(): void
    {
        // ✅ Check terms agreement
        if (!$this->agreedToTerms) {
            $this->errorMessage = 'You must agree to the Terms and Conditions before logging in.';
            return;
        }

        $key = 'admin-login:' . request()->ip();

        // ✅ Check rate limiter — 5 attempts, 5 minutes
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            $this->errorMessage = "Too many login attempts. Please try again in {$minutes} minute(s).";
            return;
        }

        $this->validate();

        try {
            $this->form->authenticate();
        } catch (\Exception $e) {
            // ✅ Increment attempts on failure
            RateLimiter::hit($key, 300); // 300 seconds = 5 minutes
            $attempts   = RateLimiter::attempts($key);
            $remaining  = max(0, 5 - $attempts);

            if ($remaining > 0) {
                $this->errorMessage = "Invalid credentials. {$remaining} attempt(s) remaining before lockout.";
            } else {
                $this->errorMessage = 'Too many login attempts. Please try again in 5 minutes.';
            }
            return;
        }

        // ✅ Clear rate limiter on success
        RateLimiter::clear($key);

        Session::regenerate();

        AdminDashboardController::logAction(
            'admin_login',
            'Admin logged in: ' . $this->form->email
        );

        $this->redirect(
            route('admin.index', absolute: false),
            navigate: true
        );
    }
}; ?>

<div class="w-full max-w-md">

    {{-- TERMS AND CONDITIONS MODAL --}}
    <div id="adminTermsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-5 border-b border-gray-100">
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-900">Terms and Conditions</h2>
                <button onclick="document.getElementById('adminTermsModal').classList.add('hidden')"
                        class="p-2 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="overflow-y-auto flex-1 px-8 py-6 space-y-4 text-sm text-gray-600 leading-relaxed">
                <p class="text-[10px] font-black uppercase tracking-widest text-blue-600">
                    Catalino D. Cerezo National High School — Enhance Voting System (Admin)
                </p>
                <p>By logging in as an administrator, you agree to the following terms and conditions:</p>

                <div class="space-y-3">
                    <div>
                        <p class="font-bold text-gray-800">1. Authorized Personnel Only</p>
                        <p>Only authorized school administrators are permitted to access this system. Unauthorized access is strictly prohibited and subject to disciplinary action.</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">2. Responsibility</p>
                        <p>As an administrator, you are fully responsible for all actions performed under your account including creating elections, managing voters, and viewing results.</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">3. Data Confidentiality</p>
                        <p>All voter data, election results, and system information must be kept strictly confidential. Sharing of sensitive data with unauthorized persons is prohibited.</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">4. Account Security</p>
                        <p>You must keep your login credentials secure. Do not share your password with anyone. Report any suspicious activity to the system owner immediately.</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">5. Integrity of Elections</p>
                        <p>You must ensure the integrity and fairness of all elections. Any manipulation of election data is strictly prohibited and may result in legal consequences.</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">6. Audit Trail</p>
                        <p>All admin actions are logged in the system's audit trail. You consent to monitoring of your activities within this system.</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800">7. Data Privacy</p>
                        <p>All data handling must comply with the Data Privacy Act of 2012 (RA 10173). Voter information must only be used for election purposes.</p>
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
                    document.getElementById('adminTermsCheck').checked = true;
                    document.getElementById('adminTermsModal').classList.add('hidden');
                "
                    class="w-full py-3 bg-blue-700 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-800 transition-all">
                    I Agree & Close
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-300">

        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Login</h1>
            <p class="text-sm text-gray-600">Enter your credentials to access the dashboard</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        {{-- ✅ Error message display --}}
        @if($errorMessage)
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl font-medium">
                {{ $errorMessage }}
            </div>
        @endif

        <form wire:submit="login" class="space-y-6">

            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Admin Email</label>
                <input
                    wire:model="form.email"
                    id="email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@school.edu.ph"
                    class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input
                        wire:model="form.password"
                        id="adminPassword"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 pr-12"
                    />
                    <button type="button" id="toggleAdminPassword"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-blue-600"
                            tabindex="-1">
                        <svg id="adminEyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <div class="flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">Remember me</label>
            </div>

            {{-- ✅ Terms and Conditions Checkbox --}}
            <div class="flex items-start gap-2">
                <input
                    id="adminTermsCheck"
                    type="checkbox"
                    wire:model="agreedToTerms"
                    class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                >
                <label for="adminTermsCheck" class="text-sm text-gray-600">
                    I agree to the
                    <button type="button" onclick="document.getElementById('adminTermsModal').classList.remove('hidden')"
                            class="text-blue-600 hover:underline font-semibold">
                        Terms and Conditions
                    </button>
                    of the Enhance Voting System.
                </label>
            </div>

            <div class="text-center">
                <a href="{{ route('admin.register') }}" wire:navigate
                   class="text-sm text-blue-600 hover:underline font-semibold">
                    Create Admin Account
                </a>
            </div>

            <button type="submit"
                    class="w-full h-12 bg-blue-700 text-white text-base font-semibold rounded-lg hover:bg-blue-800 transition shadow-md active:scale-95">
                Login
            </button>

            @if (Route::has('password.request'))
                <div class="mt-4 text-center">
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline" wire:navigate>
                        Forgot your password?
                    </a>
                </div>
            @endif

        </form>
    </div>
</div>

@script
<script>
    document.getElementById('toggleAdminPassword').addEventListener('click', function () {
        const input = document.getElementById('adminPassword');
        const icon  = document.getElementById('adminEyeIcon');

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
    });
</script>
@endscript