<x-customer-layout>
    <x-slot name="title">Complete Registration</x-slot>

    {{-- Step indicator --}}
    <div class="flex items-center gap-2 mb-7">
        <div class="flex items-center gap-1.5">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">1</span>
            <span class="text-xs font-medium text-slate-400">Identify</span>
        </div>
        <div class="flex-1 h-px bg-slate-200 mx-1"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">2</span>
            <span class="text-xs font-medium text-indigo-600">Register</span>
        </div>
        <div class="flex-1 h-px bg-slate-200 mx-1"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-400 text-xs font-bold flex items-center justify-center">3</span>
            <span class="text-xs font-medium text-slate-400">Verify</span>
        </div>
    </div>

    <div class="text-center mb-7">
        <h1 class="text-2xl font-bold text-slate-800">Create your account</h1>
        <p class="text-sm text-slate-500 mt-1">
            Continuing as
            <span class="font-medium text-slate-700">{{ $identifier }}</span>
        </p>
    </div>

    <form method="POST" action="{{ route('customer.auth.register') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1.5">First Name</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                    autofocus placeholder="Jane"
                    class="w-full px-4 py-2.5 rounded-xl border @error('first_name') border-red-400 bg-red-50 @else border-slate-300 @enderror
                           text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition" />
                @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1.5">Last Name</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                    placeholder="Doe"
                    class="w-full px-4 py-2.5 rounded-xl border @error('last_name') border-red-400 bg-red-50 @else border-slate-300 @enderror
                           text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition" />
                @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        @if($identifierType === 'phone')
            {{-- Came in with phone → also need email for OTP --}}
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                An email is required to receive your verification code (phone OTP is currently disabled).
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    placeholder="you@example.com"
                    class="w-full px-4 py-2.5 rounded-xl border @error('email') border-red-400 bg-red-50 @else border-slate-300 @enderror
                           text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition" />
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        @else
            {{-- Came in with email → optionally add phone --}}
            <div>
                <label for="phone_number" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Phone Number <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}"
                    placeholder="+1 555 000 0000"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300
                           text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition" />
            </div>
        @endif

        <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition text-sm shadow-sm mt-2">
            Create Account & Send Code →
        </button>
    </form>

    <p class="text-center text-xs text-slate-400 mt-5">
        Wrong identifier?
        <a href="{{ route('customer.auth.start') }}" class="text-indigo-500 hover:text-indigo-700 underline">Start over</a>
    </p>
</x-customer-layout>
