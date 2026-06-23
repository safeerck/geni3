<x-customer-layout>
    <x-slot name="title">Verify Your Code</x-slot>

    {{-- Step indicator --}}
    <div class="flex items-center gap-2 mb-7">
        <div class="flex items-center gap-1.5">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">✓</span>
            <span class="text-xs font-medium text-slate-400">Identify</span>
        </div>
        <div class="flex-1 h-px bg-indigo-200 mx-1"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">✓</span>
            <span class="text-xs font-medium text-slate-400">Details</span>
        </div>
        <div class="flex-1 h-px bg-slate-200 mx-1"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">3</span>
            <span class="text-xs font-medium text-indigo-600">Verify</span>
        </div>
    </div>

    <div class="text-center mb-7">
        <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Check your inbox</h1>
        @if($customer->email)
            <p class="text-sm text-slate-500 mt-1">
                We sent a 6-digit code to<br/>
                <span class="font-medium text-slate-700">{{ $customer->email }}</span>
            </p>
        @else
            <p class="text-sm text-slate-500 mt-1">We sent a 6-digit code to your phone.</p>
        @endif
    </div>

    {{-- Info / error flash --}}
    @if(session('info'))
        <div class="mb-5 flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('info') }}
        </div>
    @endif

    <form method="POST" action="{{ route('customer.auth.verify') }}" class="space-y-5">
        @csrf

        <div>
            <label for="otp" class="block text-sm font-medium text-slate-700 mb-1.5 text-center">
                Enter your 6-digit code
            </label>
            <input
                id="otp"
                type="text"
                name="otp"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                autofocus
                autocomplete="one-time-code"
                placeholder="000000"
                class="w-full px-4 py-3.5 rounded-xl border @error('otp') border-red-400 bg-red-50 @else border-slate-300 @enderror
                       text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-center
                       text-2xl font-mono tracking-widest transition"
            />
            @error('otp')
                <p class="mt-1.5 text-xs text-red-600 text-center flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <p class="text-xs text-center text-slate-400">Code expires in <strong>10 minutes</strong></p>

        <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition text-sm shadow-sm">
            Verify & Sign In
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col items-center gap-3">
        <form method="POST" action="{{ route('customer.auth.resend') }}">
            @csrf
            <button type="submit" class="text-sm text-indigo-500 hover:text-indigo-700 underline transition">
                Resend code
            </button>
        </form>
        <a href="{{ route('customer.auth.start') }}" class="text-xs text-slate-400 hover:text-slate-600 transition">
            ← Start over with a different email or phone
        </a>
    </div>
</x-customer-layout>
