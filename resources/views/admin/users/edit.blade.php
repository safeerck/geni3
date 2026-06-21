<x-app-layout>
    <x-slot name="title">Edit User</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Edit User</h1>
                <p class="text-sm text-slate-500 mt-0.5">Update account details for {{ $user->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl space-y-5">
        {{-- User card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
                 style="background-color: hsl({{ (ord($user->name[0]) * 37) % 360 }}, 60%, 55%)">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <p class="text-xs text-slate-400 mt-1">Member since {{ $user->created_at->format('F j, Y') }}</p>
            </div>
            @if($user->email_verified_at)
                <span class="ml-auto inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                </span>
            @else
                <span class="ml-auto inline-flex items-center gap-1.5 text-xs font-medium text-amber-600 bg-amber-50 px-3 py-1.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Unverified
                </span>
            @endif
        </div>

        {{-- Edit form --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border @error('name') border-red-400 bg-red-50 @else border-slate-300 bg-white @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition" />
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border @error('email') border-red-400 bg-red-50 @else border-slate-300 bg-white @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition" />
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-sm font-medium text-slate-700 mb-3">Change Password <span class="text-slate-400 font-normal">(leave blank to keep current)</span></p>
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                            <input id="password" type="password" name="password"
                                class="w-full px-4 py-2.5 rounded-lg border @error('password') border-red-400 bg-red-50 @else border-slate-300 bg-white @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition"
                                placeholder="••••••••" />
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition"
                                placeholder="••••••••" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <input id="verified" type="checkbox" name="verified" value="1"
                        {{ old('verified', $user->email_verified_at ? '1' : '') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                    <div>
                        <label for="verified" class="text-sm font-medium text-slate-700 cursor-pointer">Email verified</label>
                        <p class="text-xs text-slate-500">User can log in without email verification</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition text-sm shadow-sm">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="px-6 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-lg transition text-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Danger zone --}}
        @if($user->id !== auth()->id())
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-red-700 mb-1">Danger Zone</h3>
            <p class="text-xs text-slate-500 mb-4">Permanently delete this user account. This action cannot be undone.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    Delete User
                </button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
