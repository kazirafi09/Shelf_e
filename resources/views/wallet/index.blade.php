@extends('layouts.dashboard')

@section('page-title', 'My Wallet')

@section('dashboard-content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="flex items-center gap-3 px-5 py-4 mb-6 text-sm font-medium text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl">
        <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flex items-center gap-3 px-5 py-4 mb-6 text-sm font-medium text-red-800 bg-red-50 border border-red-200 rounded-xl">
        <svg class="w-5 h-5 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- Balance Card --}}
<div class="relative overflow-hidden p-8 rounded-2xl bg-gradient-to-br from-gray-700 to-gray-900 shadow-xl shadow-gray-800/20 text-white">
    <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/5"></div>
    <div class="absolute -bottom-12 -left-8 w-56 h-56 rounded-full bg-white/5"></div>

    <p class="text-sm font-semibold tracking-widest uppercase text-gray-200 mb-1">Current Balance</p>
    <div class="flex items-end gap-3 relative">
        <span class="text-6xl font-extrabold tracking-tight">{{ number_format($coin_balance) }}</span>
        <span class="mb-2 text-xl font-semibold text-gray-300">coins</span>
    </div>
    <p class="mt-3 text-sm text-gray-200 relative">Earn 10 coins for every ৳100 spent. Redeem for shipping discounts below.</p>

    @if($coin_balance > 0)
        <a href="{{ route('checkout.index') }}"
           class="relative inline-flex items-center gap-2 mt-5 px-5 py-2.5 text-sm font-bold text-gray-900 bg-white rounded-xl hover:bg-gray-50 transition-colors active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Use Coins at Checkout
        </a>
    @endif
</div>

{{-- Active (unused) rewards --}}
@if($activeRewards->isNotEmpty())
<div class="overflow-hidden bg-card text-card-foreground border border-emerald-200 rounded-2xl shadow-sm">
    <div class="px-6 py-4 border-b border-emerald-100 bg-emerald-50">
        <h2 class="text-base font-bold text-emerald-800">Active Rewards — Ready to Use at Checkout</h2>
    </div>
    <div class="divide-y divide-border">
        @foreach($activeRewards as $reward)
        <div class="flex items-center justify-between px-6 py-4 gap-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-foreground">৳{{ $reward->shipping_discount }} Shipping Discount</p>
                    <p class="text-xs text-muted-foreground">Redeemed for {{ number_format($reward->coins_spent) }} coins · {{ $reward->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <a href="{{ route('checkout.index') }}"
               class="shrink-0 px-4 py-2 text-xs font-bold text-emerald-700 border border-emerald-300 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                Use at Checkout
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Redeem Coins — Shipping Discount Tiers --}}
<div class="overflow-hidden bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
    <div class="px-6 py-4 border-b border-border">
        <h2 class="text-base font-bold text-foreground">Redeem Coins for Shipping Discounts</h2>
        <p class="mt-0.5 text-sm text-muted-foreground">Convert your coins into shipping discounts for your next order.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">
        @foreach($tiers as $tier)
        @php $canAfford = $coin_balance >= $tier['coins']; @endphp
        <div class="relative flex flex-col gap-4 p-5 border rounded-xl {{ $canAfford ? 'border-amber-200 bg-amber-50' : 'border-border bg-muted/40 opacity-60' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-2xl font-extrabold {{ $canAfford ? 'text-amber-700' : 'text-muted-foreground' }}">
                        ৳{{ $tier['discount'] }} Off Shipping
                    </p>
                    <p class="mt-1 text-sm font-semibold {{ $canAfford ? 'text-amber-600' : 'text-muted-foreground' }}">
                        {{ number_format($tier['coins']) }} coins
                    </p>
                </div>
                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $canAfford ? 'bg-amber-200' : 'bg-muted' }} shrink-0">
                    <svg class="w-5 h-5 {{ $canAfford ? 'text-amber-700' : 'text-muted-foreground' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
            </div>

            <form action="{{ route('wallet.redeem') }}" method="POST">
                @csrf
                <input type="hidden" name="coins" value="{{ $tier['coins'] }}">
                <button type="submit"
                        @if(!$canAfford) disabled @endif
                        class="w-full py-2.5 text-sm font-bold rounded-lg transition-colors
                               {{ $canAfford
                                    ? 'bg-amber-500 text-white hover:bg-amber-600 active:scale-95'
                                    : 'bg-muted text-muted-foreground cursor-not-allowed' }}">
                    @if($canAfford)
                        Redeem for ৳{{ $tier['discount'] }} off
                    @else
                        Need {{ number_format($tier['coins'] - $coin_balance) }} more coins
                    @endif
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>

{{-- Ledger Table --}}
<div class="overflow-hidden bg-card text-card-foreground border border-border rounded-2xl shadow-sm">
    <div class="px-6 py-4 border-b border-border">
        <h2 class="text-base font-bold text-foreground">Transaction History</h2>
    </div>

    @if($ledger->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <svg class="w-12 h-12 mb-3 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="font-semibold text-foreground">No transactions yet.</p>
            <p class="mt-1 text-sm text-muted-foreground">Earn coins by placing orders — 10 coins per ৳100 spent.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-muted">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Date</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Type</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase">Description</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase text-right">Amount</th>
                        <th class="px-6 py-3 text-xs font-bold tracking-wider text-muted-foreground uppercase text-right">Balance After</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($ledger as $entry)
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm text-foreground">{{ $entry->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-muted-foreground">{{ $entry->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($entry->type === 'credit')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    Earned
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-red-700 bg-red-50 border border-red-200 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    Spent
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground max-w-xs">{{ $entry->description }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold {{ $entry->type === 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $entry->type === 'credit' ? '+' : '−' }}{{ number_format($entry->amount) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-semibold text-foreground">{{ number_format($entry->balance_after) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-border">
            {{ $ledger->links() }}
        </div>
    @endif
</div>

@endsection
