<x-app-layout>
    <x-slot name="title">Activity Log</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Activity Log</h1>
                <p class="text-sm text-slate-500 mt-0.5">Track every action taken across the system</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live
                </span>
            </div>
        </div>
    </x-slot>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $statCards = [
                ['label' => 'Total Events',    'value' => $stats['total'],   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'bg' => 'bg-slate-50',   'text' => 'text-slate-600'],
                ['label' => 'Events Today',    'value' => $stats['today'],   'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',                                            'bg' => 'bg-indigo-50',  'text' => 'text-indigo-600'],
                ['label' => 'Logins Today',    'value' => $stats['logins'],  'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',                                    'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                ['label' => 'Changes Today',   'value' => $stats['changes'], 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',           'bg' => 'bg-amber-50',   'text' => 'text-amber-600'],
            ];
        @endphp
        @foreach($statCards as $s)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 {{ $s['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $s['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($s['value']) }}</p>
                <p class="text-xs text-slate-500">{{ $s['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters + Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Filter bar --}}
        <div class="px-5 py-4 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.activity.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <input type="text" name="actor" value="{{ request('actor') }}" placeholder="Filter by actor…"
                        class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-slate-50" />
                </div>
                <select name="action" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-slate-600">
                    <option value="">All Actions</option>
                    @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action') === $type ? 'selected' : '' }}>
                            {{ ucwords(str_replace('.', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 text-slate-600" />
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Filter</button>
                @if(request('action') || request('actor') || request('date'))
                    <a href="{{ route('admin.activity.index') }}" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium rounded-lg transition">Clear</a>
                @endif
            </form>
        </div>

        {{-- Timeline --}}
        <div class="divide-y divide-slate-100">
            @forelse($logs as $log)
            @php
                $color = $log->action_color;
                $bgMap = ['emerald'=>'bg-emerald-100','slate'=>'bg-slate-100','indigo'=>'bg-indigo-100','amber'=>'bg-amber-100','red'=>'bg-red-100','violet'=>'bg-violet-100','orange'=>'bg-orange-100'];
                $textMap = ['emerald'=>'text-emerald-600','slate'=>'text-slate-500','indigo'=>'text-indigo-600','amber'=>'text-amber-600','red'=>'text-red-600','violet'=>'text-violet-600','orange'=>'text-orange-600'];
                $dotMap = ['emerald'=>'bg-emerald-500','slate'=>'bg-slate-400','indigo'=>'bg-indigo-500','amber'=>'bg-amber-500','red'=>'bg-red-500','violet'=>'bg-violet-500','orange'=>'bg-orange-500'];
                $badgeBg = ['emerald'=>'bg-emerald-50 text-emerald-700 border-emerald-200','slate'=>'bg-slate-100 text-slate-600 border-slate-200','indigo'=>'bg-indigo-50 text-indigo-700 border-indigo-200','amber'=>'bg-amber-50 text-amber-700 border-amber-200','red'=>'bg-red-50 text-red-700 border-red-200','violet'=>'bg-violet-50 text-violet-700 border-violet-200','orange'=>'bg-orange-50 text-orange-700 border-orange-200'];
            @endphp
            <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                <div class="flex items-start gap-4">

                    {{-- Icon --}}
                    <div class="w-9 h-9 rounded-full {{ $bgMap[$color] ?? 'bg-slate-100' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 {{ $textMap[$color] ?? 'text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $log->action_icon }}"/>
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ $badgeBg[$color] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                {{ $log->action_label }}
                            </span>
                            @if($log->target_name)
                                <span class="text-xs text-slate-500">→ <span class="font-medium text-slate-700">{{ $log->target_name }}</span></span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-700">{{ $log->description }}</p>

                        {{-- Properties diff --}}
                        @if($log->properties && count($log->properties) > 0 && $log->action !== 'user.login')
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($log->properties as $field => $change)
                                @if(is_array($change) && isset($change['from'], $change['to']) && $change['from'] !== $change['to'])
                                <span class="inline-flex items-center gap-1 text-xs bg-slate-50 border border-slate-200 rounded px-2 py-1">
                                    <span class="font-medium text-slate-500">{{ $field }}:</span>
                                    <span class="text-red-500 line-through">{{ $change['from'] }}</span>
                                    <span class="text-slate-400">→</span>
                                    <span class="text-emerald-600 font-medium">{{ $change['to'] }}</span>
                                </span>
                                @endif
                            @endforeach
                        </div>
                        @endif

                        {{-- Meta --}}
                        <div class="flex flex-wrap items-center gap-3 mt-2">
                            {{-- Actor avatar + name --}}
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                     style="background-color: hsl({{ (ord($log->actor_name[0]) * 37) % 360 }}, 55%, 55%)">
                                    {{ strtoupper(substr($log->actor_name, 0, 1)) }}
                                </div>
                                <span class="text-xs text-slate-600 font-medium">{{ $log->actor_name }}</span>
                                @php $roleBadge = match($log->actor_role){ 'admin'=>'bg-indigo-100 text-indigo-600','editor'=>'bg-violet-100 text-violet-600',default=>'bg-slate-100 text-slate-500' }; @endphp
                                <span class="text-xs px-1.5 py-0.5 rounded {{ $roleBadge }}">{{ ucfirst($log->actor_role) }}</span>
                            </div>
                            @if($log->ip_address)
                            <span class="text-xs text-slate-400 font-mono">{{ $log->ip_address }}</span>
                            @endif
                            <span class="text-xs text-slate-400 ml-auto" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-20 text-center">
                <div class="flex flex-col items-center gap-3 text-slate-400">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm font-medium">No activity yet</p>
                    <p class="text-xs">Actions like logins, user changes, and role updates will appear here</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-500">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} events</p>
            <div class="flex items-center gap-1">
                @if($logs->onFirstPage())
                    <span class="px-3 py-1.5 text-xs text-slate-300 border border-slate-200 rounded-lg cursor-not-allowed">← Prev</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition">← Prev</a>
                @endif
                @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                    @if($page == $logs->currentPage())
                        <span class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition">{{ $page }}</a>
                    @endif
                @endforeach
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition">Next →</a>
                @else
                    <span class="px-3 py-1.5 text-xs text-slate-300 border border-slate-200 rounded-lg cursor-not-allowed">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
