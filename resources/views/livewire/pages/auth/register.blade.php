<?php

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $invite_code = '';

    // ← Palitan ng gusto mong secret code
    private string $validInviteCode = 'CDCNHS-ADMIN-2026';

    public function register(): void
    {
        // I-validate ang invite code muna
        if ($this->invite_code !== $this->validInviteCode) {
            $this->addError('invite_code', 'Invalid invite code.');
            return;
        }

        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('admin.index'), navigate: true);
    }
}; ?>

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-300">

        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Admin Account</h1>
            <p class="text-sm text-gray-600">Register a new system administrator</p>
        </div>

        <form wire:submit="register" class="space-y-5">

            {{-- Invite Code --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Invite Code</label>
                <input wire:model="invite_code" type="password" required
                    placeholder="Enter invite code"
                    class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"/>
                <x-input-error :messages="$errors->get('invite_code')" class="mt-2"/>
            </div>

            {{-- Name --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <input wire:model="name" type="text" required autofocus
                    placeholder="Enter your full name"
                    class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"/>
                <x-input-error :messages="$errors->get('name')" class="mt-2"/>
            </div>

            {{-- Email --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Email Address</label>
                <input wire:model="email" type="email" required
                    placeholder="admin@school.edu.ph"
                    class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"/>
                <x-input-error :messages="$errors->get('email')" class="mt-2"/>
            </div>

            {{-- Password --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input wire:model="password" type="password" required
                    placeholder="Enter password"
                    class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"/>
                <x-input-error :messages="$errors->get('password')" class="mt-2"/>
            </div>

            {{-- Confirm Password --}}
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input wire:model="password_confirmation" type="password" required
                    placeholder="Re-enter password"
                    class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"/>
            </div>

            <button type="submit"
                class="w-full h-12 bg-blue-700 text-white text-base font-semibold rounded-lg hover:bg-blue-800 transition shadow-md active:scale-95">
                Create Admin Account
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" wire:navigate
                   class="text-sm text-blue-600 hover:underline">
                    Already have an account? Login
                </a>
            </div>

        </form>
    </div>
</div>