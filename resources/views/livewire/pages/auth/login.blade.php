<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(
            default: route('dashboard.index', absolute: false),
            navigate: true
        );
    }
}; ?>

<div class="w-full max-w-md">

    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-300">

        <!-- Icon -->
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-8 h-8 text-blue-700"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3l7 4v5c0 5-3.5 9-7 10-3.5-1-7-5-7-10V7l7-4z" />
                </svg>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-900 mb-2">
                Admin Login
            </h1>
            <p class="text-sm text-gray-600">
                Administrator access to the voting system
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login Form -->
        <form wire:submit="login" class="space-y-6">

            <!-- Email -->
            <div>
                <x-input-label for="email" value="Email Address" />
                <x-text-input
                    wire:model="form.email"
                    id="email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your email"
                    class="block w-full h-12 mt-1"
                />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input
                    wire:model="form.password"
                    id="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    class="block w-full h-12 mt-1"
                />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input
                    wire:model="form.remember"
                    id="remember"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                >
                <label for="remember" class="ml-2 text-sm text-gray-600">
                    Remember me
                </label>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                class="w-full h-12 bg-blue-700 text-white text-base font-semibold rounded-lg hover:bg-blue-800 transition">
                Login as Administrator
            </button>

            <!-- Forgot Password -->
            @if (Route::has('password.request'))
                <div class="text-center">
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-blue-600 hover:underline"
                       wire:navigate>
                        Forgot your password?
                    </a>
                </div>
            @endif

        </form>
    </div>

    <!-- Warning -->
    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-300 rounded-lg">
        <p class="text-sm text-yellow-800 text-center">
            <strong>Note:</strong> This area is restricted to authorized administrators only.
        </p>
    </div>

</div>
