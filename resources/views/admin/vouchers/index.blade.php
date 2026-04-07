@extends('layouts.admin')

@section('title', 'Vouchers')
@section('subtitle', 'Create and manage discount vouchers for your customers.')

@section('admin-content')

@if(session('success'))
    <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl">
        {{ session('success') }}
    </div>
@endif

<div class="flex justify-end mb-6">
    <a href="{{ route('admin.vouchers.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold transition bg-primary text-primary-foreground hover:bg-primary/90 rounded-xl active:scale-95">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Voucher
    </a>
</div>

<div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl">
    <table class="w-full text-left">
        <thead class="bg-muted">
            <tr>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Code</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Discount</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Uses</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Expires</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Status</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Announced</th>
                <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border">
            @forelse($vouchers as $voucher)
            <tr class="transition hover:bg-muted/50">
                <td class="px-6 py-4">
                    <span class="font-mono text-sm font-bold tracking-widest text-foreground bg-muted px-2 py-1 rounded">
                        {{ $voucher->code }}
                    </span>
                    @if($voucher->description)
                        <p class="mt-1 text-xs text-muted-foreground">{{ $voucher->description }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm font-semibold text-foreground">
                    @if($voucher->discount_type === 'percentage')
                        {{ $voucher->discount_value }}% off
                    @else
                        ৳{{ number_format($voucher->discount_value, 0) }} off
                    @endif
                    @if($voucher->min_order_amount > 0)
                        <p class="text-xs font-normal text-muted-foreground">Min. order ৳{{ number_format($voucher->min_order_amount, 0) }}</p>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-muted-foreground">
                    {{ $voucher->used_count }}
                    @if($voucher->max_uses)
                        / {{ $voucher->max_uses }}
                    @else
                        / ∞
                    @endif
                    <p class="text-xs">{{ $voucher->max_uses_per_user }}x per user</p>
                </td>
                <td class="px-6 py-4 text-sm text-muted-foreground">
                    @if($voucher->expires_at)
                        <span class="{{ $voucher->expires_at->isPast() ? 'text-red-500 font-semibold' : '' }}">
                            {{ $voucher->expires_at->format('d M Y') }}
                        </span>
                    @else
                        <span class="text-muted-foreground">No expiry</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($voucher->isUsable())
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-green-700 bg-green-100 rounded-full">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">
                            Inactive
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($voucher->is_announced)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-100 rounded-full">
                            Announced
                        </span>
                    @else
                        <span class="text-xs text-muted-foreground">—</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.vouchers.edit', $voucher) }}"
                           class="inline-flex items-center px-4 py-2 text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 rounded-lg transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST"
                              onsubmit="return confirm('Delete voucher {{ $voucher->code }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 text-xs font-bold bg-destructive text-destructive-foreground hover:bg-destructive/90 rounded-lg transition-colors">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-16 text-sm font-medium text-center text-muted-foreground">
                    No vouchers yet.
                    <a href="{{ route('admin.vouchers.create') }}" class="block mt-1 font-bold text-cyan-600 hover:underline">
                        Create the first voucher &rarr;
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
