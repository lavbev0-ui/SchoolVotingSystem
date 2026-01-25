<x-guest-layout>
    <div class="w-full max-w-md">

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-300">

            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <!-- User Icon -->
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

            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Student Login
                </h1>
                <p class="text-sm text-gray-600">
                    Enter your credentials to access the voting system
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('voter.login') }}" class="space-y-6">
                @csrf

                <!-- Student ID -->
                <div class="space-y-2">
                    <label for="userID" class="block text-sm font-medium text-gray-700">
                        Student ID
                    </label>
                    <input
                        id="userID"
                        name="userID"
                        type="text"
                        value="{{ old('userID') }}"
                        required
                        autofocus
                        placeholder="Enter your student ID"
                        class="block w-full h-12 rounded-md border-gray-300 shadow-sm
                               focus:border-blue-600 focus:ring-blue-600"
                    >
                    <x-input-error :messages="$errors->get('userID')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="block w-full h-12 rounded-md border-gray-300 shadow-sm
                               focus:border-blue-600 focus:ring-blue-600"
                    >
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input
                        id="remember_me"
                        name="remember"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                    >
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full h-12 bg-blue-700 text-white text-base font-semibold
                           rounded-lg hover:bg-blue-800 transition">
                    Login
                </button>
            </form>

            <!-- Forgot Password -->
            <div class="mt-6 text-center">
                <a href="#" class="text-sm text-blue-600 hover:underline">
                    Forgot your password?
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Need help? Contact your school administrator</p>
        </div>

    </div>
</x-guest-layout>
