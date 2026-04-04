@extends('layouts.dashboard')

@section('page-title', 'My Wallet')

@section('dashboard-content')

{{-- Balance Card --}}
<div class="relative overflow-hidden p-8 rounded-2xl bg-gradient-to-br from-cyan-600 to-cyan-800 shadow-xl shadow-cyan-700/20 text-white">
    <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/5"></div>
    <div class="absolute -bottom-12 -left-8 w-56 h-56 rounded-full bg-white/5"></div>

    <p class="text-sm font-semibold tracking-widest uppercase text-cyan-200 mb-1">Current Balance</p>
    <div class="flex items-end gap-3 relative">
        <span class="text-6xl font-extrabold tracking-tight">{{ number_format($coin_balance) }}</span>
        <span class="mb-2 text-xl font-semibold text-cyan-300">coins</span>
    </div>
    <p class="mt-3 text-sm text-cyan-200 relative">1 coin = ৳1 off your next order.</p>

    @if($coin_balance > 0)
        <a href="/checkout"
           class="relative inline-flex items-center gap-2 mt-5 px-5 py-2.5 text-sm font-bold text-cyan-800 bg-white rounded-xl hover:bg-cyan-50 transition-colors active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Use Coins at Checkout
        </a>
    @endif
</div>

{{-- Ledger Table --}}
<div class="overflow-hidden bg-card text-card-foreground rounded-2xl shadow-sm ring-1 ring-border">
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
            <p class="mt-1 text-sm text-muted-foreground">Earn coins by shopping or through promotions.</p>
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
                                    Credit
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-red-700 bg-red-50 border border-red-200 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    Debit
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
