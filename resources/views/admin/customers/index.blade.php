<x-app-layout>
    <x-slot name="title">Customers</x-slot>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Customers</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage customer accounts</p>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Total',      'value' => $total,      'color' => 'slate'],
            ['label' => 'Verified',   'value' => $verified,   'color' => 'emerald'],
            ['label' => 'Unverified', 'value' => $unverified, 'color' => 'amber'],
            ['label' => 'Today',      'value' => $today,      'color' => 'indigo'],
        ] as $stat)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
                <p class="text-xs text-slate-500">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap gap-3 mb-5">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, email, phone…"
               class="flex-1 min-w-48 text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="verified" class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">All statuses</option>
            <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
            <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
            Filter
        </button>
        @if(request('search') || request('verified') !== null && request('verified') !== '')
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg transition">
                Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Contact</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Joined</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="font-medium text-slate-800 hover:text-indigo-600">
                                {{ $customer->full_name }}
                            </a>
                            <p class="text-xs text-slate-400">#{{ $customer->id }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            @if($customer->email)
                                <p>{{ $customer->email }}</p>
                            @endif
                            @if($customer->phone_number)
                                <p class="text-xs text-slate-400">{{ $customer->phone_number }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($customer->is_verified)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Unverified
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $customer->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.customers.show', $customer) }}"
                                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
                                      onsubmit="return confirm('Delete this customer?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400 text-sm">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
