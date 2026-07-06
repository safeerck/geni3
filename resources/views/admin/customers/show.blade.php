<x-app-layout>
    <x-slot name="title">Customer — {{ $customer->full_name }}</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $customer->full_name }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">Customer #{{ $customer->id }}</p>
            </div>
            <a href="{{ route('admin.customers.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Back</a>
        </div>
    </x-slot>

    <div class="max-w-lg space-y-5">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">

            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Account Details</h2>
                @if($customer->is_verified)
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Unverified
                    </span>
                @endif
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">ID</dt>
                    <dd class="font-medium text-slate-800">#{{ $customer->id }}</dd>
                </div>
                @if($customer->first_name || $customer->last_name)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Name</dt>
                    <dd class="font-medium text-slate-800">{{ $customer->full_name }}</dd>
                </div>
                @endif
                @if($customer->email)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-medium text-slate-800">{{ $customer->email }}</dd>
                </div>
                @endif
                @if($customer->phone_number)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Phone</dt>
                    <dd class="font-medium text-slate-800">{{ $customer->phone_number }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-slate-500">Registered</dt>
                    <dd class="font-medium text-slate-800">{{ $customer->created_at->format('M j, Y g:i A') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Last Updated</dt>
                    <dd class="font-medium text-slate-800">{{ $customer->updated_at->format('M j, Y g:i A') }}</dd>
                </div>
            </dl>
        </div>

        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
              onsubmit="return confirm('Permanently delete this customer?')">
            @csrf @method('DELETE')
            <button type="submit"
                class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-xl border border-red-200 transition">
                Delete Customer
            </button>
        </form>

    </div>
</x-app-layout>
