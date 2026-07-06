<x-customer-layout>
    <x-slot name="title">One More Step</x-slot>

    <div class="text-center mb-7">
        <h1 class="text-2xl font-bold text-slate-800">Add your email</h1>
        <p class="text-sm text-slate-500 mt-1">
            You entered <span class="font-medium text-slate-700">{{ $identifier }}</span>
        </p>
    </div>

    <div class="mb-5 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 flex items-start gap-2">
        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        SMS verification is currently disabled. Please provide an email address to receive your verification code.
    </div>

    <form method="POST" action="{{ route('customer.auth.register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                autofocus placeholder="you@example.com"
                class="w-full px-4 py-3 rounded-xl border @error('email') border-red-400 bg-red-50 @else border-slate-300 @enderror
                       text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition" />
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition text-sm shadow-sm">
            Send Verification Code →
        </button>
    </form>

    <p class="text-center text-xs text-slate-400 mt-5">
        Wrong number?
        <a href="{{ route('customer.auth.start') }}" class="text-indigo-500 hover:text-indigo-700 underline">Start over</a>
    </p>
</x-customer-layout>
