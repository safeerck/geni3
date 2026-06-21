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
            <div class="flex-1">
                <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <p class="text-xs text-slate-400 mt-1">Member since {{ $user->created_at->format('F j, Y') }}</p>
            </div>
            @php $roleBg = match($user->role) { 'admin'=>'bg-indigo-50 text-indigo-700', 'editor'=>'bg-violet-50 text-violet-700', default=>'bg-slate-100 text-slate-600' }; @endphp
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $roleBg }}">{{ $user->role_label }}</span>
        </div>

        {{-- Edit form --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            @if(session('error'))
                <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border @error('name') border-red-400 bg-red-50 @else border-slate-300 @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition" />
                    @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border @error('email') border-red-400 bg-red-50 @else border-slate-300 @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition" />
                    @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Role picker --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                    @php $roles = [
                        ['value'=>'admin',  'label'=>'Admin',  'desc'=>'Full access to all features',   'color'=>'indigo', 'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                        ['value'=>'editor', 'label'=>'Editor', 'desc'=>'Can view and edit content',      'color'=>'violet', 'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        ['value'=>'viewer', 'label'=>'Viewer', 'desc'=>'Read-only access to content',    'color'=>'slate',  'icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                    ]; @endphp
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($roles as $r)
                        <label class="relative cursor-pointer {{ ($user->id === auth()->id() && $r['value'] !== 'admin') ? 'opacity-50 cursor-not-allowed' : '' }}">
                            <input type="radio" name="role" value="{{ $r['value'] }}" class="peer sr-only"
                                   {{ old('role', $user->role) === $r['value'] ? 'checked' : '' }}
                                   {{ ($user->id === auth()->id() && $r['value'] !== 'admin') ? 'disabled' : '' }} />
                            <div class="p-3.5 rounded-xl border-2 border-slate-200 peer-checked:border-{{ $r['color'] }}-500 peer-checked:bg-{{ $r['color'] }}-50 hover:border-slate-300 transition text-center">
                                <div class="w-8 h-8 rounded-lg bg-{{ $r['color'] }}-100 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-{{ $r['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $r['icon'] }}"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-800">{{ $r['label'] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 leading-tight">{{ $r['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @if($user->id === auth()->id())
                        <p class="mt-2 text-xs text-slate-500">You cannot change your own role.</p>
                    @endif
                    @error('role')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-sm font-medium text-slate-700 mb-3">Change Password <span class="text-slate-400 font-normal">(leave blank to keep current)</span></p>
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                            <input id="password" type="password" name="password"
                                class="w-full px-4 py-2.5 rounded-lg border @error('password') border-red-400 bg-red-50 @else border-slate-300 @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition"
                                placeholder="••••••••" />
                            @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition"
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
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition text-sm shadow-sm">Save Changes</button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-lg transition text-sm">Cancel</a>
                </div>
            </form>
        </div>

        {{-- Danger zone --}}
        @if($user->id !== auth()->id() && auth()->user()->isAdmin())
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-red-700 mb-1">Danger Zone</h3>
            <p class="text-xs text-slate-500 mb-4">Permanently delete this user. This cannot be undone.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">Delete User</button>
            </form>
        </div>
        @endif
    </div>
</x-app-layout>
