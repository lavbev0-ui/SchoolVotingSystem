<x-guest-layout>
    <div class="w-full max-w-md">

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-300">

            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>

            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h1>
                <p class="text-sm text-gray-600">
                    Ilagay ang iyong email address at magpapadala kami ng link para ma-reset ang iyong password.
                </p>
            </div>

            {{-- Success Status --}}
            @if(session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-300 rounded-lg text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('voter.password.email') }}" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email Address
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="Ilagay ang iyong email"
                        class="block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full h-12 bg-blue-700 text-white text-base font-semibold rounded-lg hover:bg-blue-800 transition shadow-md active:scale-95">
                    Magpadala ng Reset Link
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('voter.login') }}"
                   class="text-sm text-blue-600 hover:underline flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Bumalik sa Login
                </a>
            </div>
        </div>

        <div class="mt-6 text-center text-sm text-gray-500 font-medium">
            <p>Need help? Contact your school administrator</p>
        </div>

    </div>
</x-guest-layout>