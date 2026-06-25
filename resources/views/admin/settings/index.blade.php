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

    @if(session('error'))
        <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif
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

        </div>

        <!-- Email SMTP Settings -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Email (SMTP) Settings</p>
                    <p class="text-xs text-slate-500">Configure outgoing email delivery for notifications and system messages</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="px-6 py-5 space-y-5">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="smtp">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Host -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">SMTP Host</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']->value ?? '') }}"
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g. smtp.gmail.com">
                        @error('mail_host')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Port -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Port</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']->value ?? '') }}"
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g. 587">
                        @error('mail_port')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Username</label>
                        <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username']->value ?? '') }}"
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="SMTP username">
                        @error('mail_username')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="mail_password"
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Leave blank to keep existing">
                        @error('mail_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Encryption -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Encryption</label>
                        <select name="mail_encryption" class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">None</option>
                            <option value="tls" {{ (old('mail_encryption', $settings['mail_encryption']->value ?? '') === 'tls') ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ (old('mail_encryption', $settings['mail_encryption']->value ?? '') === 'ssl') ? 'selected' : '' }}>SSL</option>
                            <option value="starttls" {{ (old('mail_encryption', $settings['mail_encryption']->value ?? '') === 'starttls') ? 'selected' : '' }}>STARTTLS</option>
                        </select>
                        @error('mail_encryption')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- From Address -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">From Address</label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']->value ?? '') }}"
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="sender@example.com">
                        @error('mail_from_address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- From Name -->
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">From Name</label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']->value ?? config('mail.from.name')) }}"
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="{{ config('mail.from.name') }}">
                        @error('mail_from_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Save SMTP Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Test Email Section -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Test Email Configuration</p>
                    <p class="text-xs text-slate-500">Send a test message to verify your SMTP settings</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="px-6 py-5 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="section" value="test_email">

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-slate-700 mb-1.5">Recipient Email</label>
                        <input type="email" name="test_email" value="{{ old('test_email', auth()->user()->email) }}"
                               class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="your-email@example.com">
                        @error('test_email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 w-full sm:w-auto">
                            Send Test Email
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Future settings placeholder -->
        <div class="bg-white rounded-xl border border-dashed border-slate-200 p-6 text-center">
            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <p class="text-sm text-slate-400 font-medium">More settings coming soon</p>
            <p class="text-xs text-slate-400 mt-1">Email provider, branding, notifications…</p>
        </div>
            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            <p class="text-sm text-slate-400 font-medium">More settings coming soon</p>
            <p class="text-xs text-slate-400 mt-1">Email provider, branding, notifications…</p>
        </div>

    </div>
</x-app-layout>
