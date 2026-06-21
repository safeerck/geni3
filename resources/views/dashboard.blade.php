<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Dashboard</h1>
                <p class="text-sm text-slate-500 mt-0.5">Welcome back, {{ Auth::user()->name }}! Here's what's happening.</p>
            </div>
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>
    </x-slot>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        @php
        $stats = [
            ['label' => 'Total Users',     'value' => '2,847',  'change' => '+12.5%', 'up' => true,  'color' => 'indigo',  'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Revenue',         'value' => '$48,295', 'change' => '+8.2%',  'up' => true,  'color' => 'emerald', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Active Sessions', 'value' => '1,234',  'change' => '+3.1%',  'up' => true,  'color' => 'violet',  'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            ['label' => 'Bounce Rate',     'value' => '24.3%',  'change' => '-2.4%',  'up' => false, 'color' => 'rose',    'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ];
        @endphp

        @foreach ($stats as $stat)
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-start gap-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                @if($stat['color'] === 'indigo')  bg-indigo-50
                @elseif($stat['color'] === 'emerald') bg-emerald-50
                @elseif($stat['color'] === 'violet')  bg-violet-50
                @else bg-rose-50 @endif">
                <svg class="w-5 h-5
                    @if($stat['color'] === 'indigo')  text-indigo-600
                    @elseif($stat['color'] === 'emerald') text-emerald-600
                    @elseif($stat['color'] === 'violet')  text-violet-600
                    @else text-rose-600 @endif"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-slate-500 font-medium">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stat['value'] }}</p>
                <p class="flex items-center gap-1 text-xs mt-1 font-medium {{ $stat['up'] ? 'text-emerald-600' : 'text-rose-500' }}">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $stat['up'] ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                    </svg>
                    {{ $stat['change'] }} vs last month
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts + Activity Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

        {{-- Revenue chart --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-semibold text-slate-800">Revenue Overview</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Monthly revenue for the past 6 months</p>
                </div>
                <span class="text-xs bg-emerald-50 text-emerald-600 font-semibold px-2.5 py-1 rounded-full">+8.2% MoM</span>
            </div>
            {{-- Bar chart --}}
            <div class="flex items-end gap-3 h-40">
                @php
                $bars = [
                    ['h' => '50%',  'label' => 'Jan', 'active' => false],
                    ['h' => '65%',  'label' => 'Feb', 'active' => false],
                    ['h' => '45%',  'label' => 'Mar', 'active' => false],
                    ['h' => '78%',  'label' => 'Apr', 'active' => false],
                    ['h' => '60%',  'label' => 'May', 'active' => false],
                    ['h' => '100%', 'label' => 'Jun', 'active' => true],
                ];
                @endphp
                @foreach ($bars as $bar)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full rounded-t-md transition-all {{ $bar['active'] ? 'bg-indigo-600' : 'bg-slate-100 hover:bg-indigo-200' }}"
                         style="height: {{ $bar['h'] }}"></div>
                    <span class="text-xs text-slate-400">{{ $bar['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Traffic sources --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="mb-5">
                <h3 class="font-semibold text-slate-800">Traffic Sources</h3>
                <p class="text-xs text-slate-500 mt-0.5">Where your users come from</p>
            </div>
            <div class="space-y-4">
                @php
                $sources = [
                    ['name' => 'Organic Search', 'pct' => 42, 'color' => 'bg-indigo-500'],
                    ['name' => 'Direct',          'pct' => 28, 'color' => 'bg-violet-500'],
                    ['name' => 'Social Media',    'pct' => 18, 'color' => 'bg-emerald-500'],
                    ['name' => 'Referral',        'pct' => 12, 'color' => 'bg-amber-500'],
                ];
                @endphp
                @foreach ($sources as $src)
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-slate-600 font-medium">{{ $src['name'] }}</span>
                        <span class="text-slate-500">{{ $src['pct'] }}%</span>
                    </div>
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="{{ $src['color'] }} h-full rounded-full" style="width: {{ $src['pct'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Summary --}}
            <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-2 gap-3">
                <div class="bg-indigo-50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-indigo-700">8.4k</p>
                    <p class="text-xs text-slate-500 mt-0.5">Visitors</p>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-emerald-700">3.2%</p>
                    <p class="text-xs text-slate-500 mt-0.5">Conv. rate</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Users + Quick Actions --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Recent Users Table --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div>
                    <h3 class="font-semibold text-slate-800">Recent Users</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Latest registered accounts</p>
                </div>
                <a href="#" class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">User</th>
                            <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">Role</th>
                            <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">Joined</th>
                            <th class="text-left text-xs font-semibold text-slate-500 px-5 py-3 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                        $users = [
                            ['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'role' => 'Admin',   'date' => 'Jun 18, 2025', 'active' => true],
                            ['name' => 'Bob Smith',     'email' => 'bob@example.com',   'role' => 'Editor',  'date' => 'Jun 15, 2025', 'active' => true],
                            ['name' => 'Carol White',   'email' => 'carol@example.com', 'role' => 'Viewer',  'date' => 'Jun 12, 2025', 'active' => false],
                            ['name' => 'David Lee',     'email' => 'david@example.com', 'role' => 'Editor',  'date' => 'Jun 10, 2025', 'active' => true],
                            ['name' => 'Eva Brown',     'email' => 'eva@example.com',   'role' => 'Viewer',  'date' => 'Jun 7, 2025',  'active' => false],
                        ];
                        @endphp
                        @foreach ($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($user['name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800 text-sm">{{ $user['name'] }}</p>
                                        <p class="text-xs text-slate-400">{{ $user['email'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                    @if($user['role'] === 'Admin') bg-indigo-50 text-indigo-700
                                    @elseif($user['role'] === 'Editor') bg-violet-50 text-violet-700
                                    @else bg-slate-100 text-slate-600 @endif">
                                    {{ $user['role'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $user['date'] }}</td>
                            <td class="px-5 py-3.5">
                                <span class="flex items-center gap-1.5 text-xs font-medium {{ $user['active'] ? 'text-emerald-600' : 'text-slate-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user['active'] ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                    {{ $user['active'] ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick Actions + System Status --}}
        <div class="space-y-5">
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4">Quick Actions</h3>
                <div class="space-y-2.5">
                    @php
                    $actions = [
                        ['label' => 'Add New User',     'color' => 'indigo', 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
                        ['label' => 'Generate Report',  'color' => 'violet', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['label' => 'Send Notification','color' => 'emerald','icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                        ['label' => 'Backup Database',  'color' => 'amber',  'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                    ];
                    @endphp
                    @foreach ($actions as $action)
                    <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition text-left group">
                        <div class="w-7 h-7 rounded-md flex items-center justify-center
                            @if($action['color']==='indigo') bg-indigo-50 group-hover:bg-indigo-100
                            @elseif($action['color']==='violet') bg-violet-50 group-hover:bg-violet-100
                            @elseif($action['color']==='emerald') bg-emerald-50 group-hover:bg-emerald-100
                            @else bg-amber-50 group-hover:bg-amber-100 @endif transition">
                            <svg class="w-3.5 h-3.5
                                @if($action['color']==='indigo') text-indigo-600
                                @elseif($action['color']==='violet') text-violet-600
                                @elseif($action['color']==='emerald') text-emerald-600
                                @else text-amber-600 @endif"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-slate-900 transition">{{ $action['label'] }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-300 ml-auto group-hover:text-slate-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- System Health --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-semibold text-slate-800 mb-4">System Health</h3>
                <div class="space-y-3">
                    @php
                    $metrics = [
                        ['label' => 'CPU Usage',    'value' => 34,  'color' => 'bg-indigo-500'],
                        ['label' => 'Memory',       'value' => 61,  'color' => 'bg-violet-500'],
                        ['label' => 'Disk Space',   'value' => 48,  'color' => 'bg-emerald-500'],
                        ['label' => 'Bandwidth',    'value' => 22,  'color' => 'bg-amber-500'],
                    ];
                    @endphp
                    @foreach ($metrics as $metric)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-600 font-medium">{{ $metric['label'] }}</span>
                            <span class="text-slate-500 font-semibold">{{ $metric['value'] }}%</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="{{ $metric['color'] }} h-full rounded-full" style="width: {{ $metric['value'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 flex items-center gap-2 text-xs text-emerald-600 font-medium">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    All systems operational
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
