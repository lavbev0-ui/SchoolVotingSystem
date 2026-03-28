<x-guest-layout>
    {{-- ✅ Dynamic message — email o phone number --}}
    @php
        $voter = Auth::guard('voter')->user();
        $hasEmail = !empty($voter->email);
        $hasPhone = !empty($voter->phone_number);
    @endphp

    <div class="mb-4 text-sm text-gray-600">
        @if($hasEmail)
           Please enter the 6-digit security code sent to your email
            (<span class="font-bold text-gray-800">{{ $voter->email }}</span>) to be able to vote.
        @elseif($hasPhone)
            Please enter the 6-digit security code sent to your phone number
            (<span class="font-bold text-gray-800">{{ $voter->phone_number }}</span>) to be able to vote.
        @else
            Please enter the 6-digit security code to vote.
        @endif
    </div>

    @if (session('success'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('voter.2fa.verify') }}">
        @csrf
        <div>
            <x-input-label for="code" value="Security Code" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" required autofocus placeholder="123456" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <button form="resend-form" type="submit" class="text-sm text-indigo-600 hover:text-indigo-900 underline">
                @if($hasEmail)
                    I didn't receive anything? Resend the code.
                @else
                    I didn't receive anything? Resend the code to my phone.
                @endif
            </button>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Verify Code
            </button>
        </div>
    </form>

    <form id="resend-form" method="POST" action="{{ route('voter.2fa.resend') }}">
        @csrf
    </form>
</x-guest-layout>