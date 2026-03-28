<x-guest-layout>
    @php $admin = Auth::guard('web')->user(); @endphp

    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-300">

            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>

            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Admin Verification</h1>
                <p class="text-sm text-gray-600">
                    A 6-digit security code was sent to
                    <span class="font-bold text-gray-800">{{ $admin->email }}</span>
                </p>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.2fa.verify') }}" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Security Code</label>
                    <input type="text" name="code" required autofocus
                           placeholder="Enter 6-digit code"
                           class="block w-full h-14 rounded-xl border-gray-300 shadow-sm text-center text-2xl font-black tracking-widest focus:border-red-600 focus:ring-red-600"/>
                    @error('code')
                        <p class="text-red-600 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full h-12 bg-red-600 text-white text-base font-semibold rounded-lg hover:bg-red-700 transition shadow-md active:scale-95">
                    Verify and Access Dashboard
                </button>
            </form>

            <form method="POST" action="{{ route('admin.2fa.resend') }}" class="mt-4 text-center">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:underline">
                    Did not receive the code? Resend.
                </button>
            </form>

            <div class="mt-3 text-center">
    <form method="POST" action="{{ route('admin.logout') }}">

        @csrf
        <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 hover:underline bg-transparent border-none cursor-pointer">
            Back to Login
        </button>
    </form>
</div>

        </div>
    </div>
</x-guest-layout>