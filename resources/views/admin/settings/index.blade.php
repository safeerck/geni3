<x-app-layout>
    <x-slot name="title">Settings</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Settings</h1>
            <p class="text-sm text-slate-500 mt-0.5">Configure system-wide options</p>
        </div>
    </x-slot>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl space-y-5">

        {{-- Customer OTP section --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Customer OTP Settings</p>
                    <p class="text-xs text-slate-500">Control how verification codes are delivered to customers</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="px-6 py-5 space-y-5">
                @csrf @method('PUT')

                @php $phoneOtpEnabled = $settings['phone_otp_enabled']->value ?? '0'; @endphp

                {{-- Phone OTP toggle --}}
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-800">Enable Phone OTP</p>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            When enabled, OTP codes can be sent via SMS to customer phone numbers.
                            When disabled, OTP is only sent by email.
                        </p>
                        @if($phoneOtpEnabled === '0')
                            <span class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Currently: Email only
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Currently: Email + Phone SMS
                            </span>
                        @endif
                    </div>

                    {{-- Toggle switch --}}
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-1">
                        <input type="hidden" name="phone_otp_enabled" value="0">
                        <input type="checkbox" name="phone_otp_enabled" value="1"
                               {{ $phoneOtpEnabled === '1' ? 'checked' : '' }}
                               onchange="this.form.submit()"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500
                                    rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                                    after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all
                                    peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                {{-- Info box about SMS integration --}}
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-amber-700">SMS Provider Not Yet Configured</p>
                            <p class="text-xs text-amber-600 mt-0.5 leading-relaxed">
                                Enabling Phone OTP requires an SMS API integration (e.g. Twilio, Vonage, AWS SNS).
                                The code is ready — add your provider credentials in
                                <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">app/Services/OtpService.php</code>
                                inside the <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">sendViaSms()</code> method.
                            </p>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        {{-- Future settings placeholder --}}
        <div class="bg-white rounded-xl border border-dashed border-slate-200 p-6 text-center">
            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <p class="text-sm text-slate-400 font-medium">More settings coming soon</p>
            <p class="text-xs text-slate-400 mt-1">Email provider, branding, notifications…</p>
        </div>

    </div>
</x-app-layout>
