<x-customer-layout>
    <x-slot name="title">Sign In or Register</x-slot>

    <div class="text-center mb-7">
        <h1 class="text-2xl font-bold text-slate-800">Welcome</h1>
        <p class="text-sm text-slate-500 mt-1">Enter your email or phone number to continue</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('customer.auth.start') }}" class="space-y-5">
        @csrf

        <div>
            <label for="identifier" class="block text-sm font-medium text-slate-700 mb-1.5">
                Email address or Phone number
            </label>
            <input
                id="identifier"
                type="text"
                name="identifier"
                value="{{ old('identifier') }}"
                autofocus
                autocomplete="email tel"
                placeholder="you@example.com or +1 555 000 0000"
                class="w-full px-4 py-3 rounded-xl border @error('identifier') border-red-400 bg-red-50 @else border-slate-300 @enderror
                       text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition"
            />
            @error('identifier')
                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition text-sm shadow-sm">
            Continue →
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center">
        <p class="text-xs text-slate-400">
            New customers will be guided through a quick registration.
        </p>
    </div>
</x-customer-layout>
