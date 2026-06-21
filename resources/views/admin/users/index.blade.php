<x-app-layout>
    <x-slot name="title">Users</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">User Management</h1>
                <p class="text-sm text-slate-500 mt-0.5">Manage users and their roles</p>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.users.create') }}"
               class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add User
            </a>
            @endif
        </div>
    </x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $roleStats = [
                ['label'=>'Total Users',  'value'=> $total,                         'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'bg'=>'bg-slate-50','text'=>'text-slate-600'],
                ['label'=>'Admins',       'value'=> $byRole['admin']  ?? 0,         'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'bg'=>'bg-indigo-50','text'=>'text-indigo-600'],
                ['label'=>'Editors',      'value'=> $byRole['editor'] ?? 0,         'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'bg'=>'bg-violet-50','text'=>'text-violet-600'],
                ['label'=>'Viewers',      'value'=> $byRole['viewer'] ?? 0,         'icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'bg'=>'bg-amber-50','text'=>'text-amber-600'],
            ];
        @endphp
        @foreach($roleStats as $s)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 {{ $s['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $s['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $s['value'] }}</p>
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Table card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Search & filter bar --}}
        <div class="px-5 py-4 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email…"
                        class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50" />
                </div>
                <select name="role" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-slate-600">
                    <option value="">All Roles</option>
                    <option value="admin"  {{ request('role')==='admin'  ? 'selected':'' }}>Admin</option>
                    <option value="editor" {{ request('role')==='editor' ? 'selected':'' }}>Editor</option>
                    <option value="viewer" {{ request('role')==='viewer' ? 'selected':'' }}>Viewer</option>
                </select>
                <select name="status" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-slate-600">
                    <option value="">All Status</option>
                    <option value="verified"   {{ request('status')==='verified'   ? 'selected':'' }}>Verified</option>
                    <option value="unverified" {{ request('status')==='unverified' ? 'selected':'' }}>Unverified</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Search</button>
                @if(request('search') || request('status') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium rounded-lg transition">Clear</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">User</th>
                        <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">Role</th>
                        <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider hidden sm:table-cell">Joined</th>
                        <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">Status</th>
                        <th class="text-right text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                     style="background-color: hsl({{ (ord($user->name[0]) * 37) % 360 }}, 60%, 55%)">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 flex items-center gap-1.5">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="text-xs bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded font-medium">You</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $roleBg = match($user->role) { 'admin'=>'bg-indigo-50 text-indigo-700', 'editor'=>'bg-violet-50 text-violet-700', default=>'bg-slate-100 text-slate-600' };
                                $roleIcon = match($user->role) {
                                    'admin'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                                    'editor' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                                    default  => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $roleBg }}">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $roleIcon }}"/></svg>
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs hidden sm:table-cell">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3.5">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Unverified
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                @if($user->id !== auth()->id() && auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium">No users found</p>
                                <p class="text-xs">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-500">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users</p>
            <div class="flex items-center gap-1">
                @if($users->onFirstPage())
                    <span class="px-3 py-1.5 text-xs text-slate-300 border border-slate-200 rounded-lg cursor-not-allowed">← Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition">← Prev</a>
                @endif
                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition">{{ $page }}</a>
                    @endif
                @endforeach
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition">Next →</a>
                @else
                    <span class="px-3 py-1.5 text-xs text-slate-300 border border-slate-200 rounded-lg cursor-not-allowed">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
