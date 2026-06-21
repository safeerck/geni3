<x-app-layout>
    <x-slot name="title">Add User</x-slot>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Add New User</h1>
                <p class="text-sm text-slate-500 mt-0.5">Create a new user account and assign a role</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 rounded-lg border @error('name') border-red-400 bg-red-50 @else border-slate-300 @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition"
                        placeholder="John Doe" />
                    @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 rounded-lg border @error('email') border-red-400 bg-red-50 @else border-slate-300 @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition"
                        placeholder="john@example.com" />
                    @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Role picker --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
                    <div class="grid grid-cols-3 gap-3">
                        @php $roles = [
                            ['value'=>'admin',  'label'=>'Admin',  'desc'=>'Full access to all features',      'color'=>'indigo', 'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            ['value'=>'editor', 'label'=>'Editor', 'desc'=>'Can view and edit content',         'color'=>'violet', 'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                            ['value'=>'viewer', 'label'=>'Viewer', 'desc'=>'Read-only access to content',       'color'=>'slate',  'icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                        ]; @endphp
                        @foreach($roles as $r)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="{{ $r['value'] }}" class="peer sr-only"
                                   {{ old('role', 'viewer') === $r['value'] ? 'checked' : '' }} />
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
                    @error('role')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-lg border @error('password') border-red-400 bg-red-50 @else border-slate-300 @enderror text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition"
                        placeholder="••••••••" />
                    @error('password')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition"
                        placeholder="••••••••" />
                </div>

                <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <input id="verified" type="checkbox" name="verified" value="1" {{ old('verified') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                    <div>
                        <label for="verified" class="text-sm font-medium text-slate-700 cursor-pointer">Mark as verified</label>
                        <p class="text-xs text-slate-500">User won't need to verify their email address</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition text-sm shadow-sm">Create User</button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-lg transition text-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
