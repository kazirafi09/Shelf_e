@extends('layouts.app')

@section('content')
<div class="container px-4 py-10 mx-auto max-w-4xl">

    {{-- Breadcrumb --}}
    <div class="mb-8 text-sm text-gray-500">
        <a href="/" class="transition hover:text-orange-500">Home</a>
        <span class="mx-2">&rsaquo;</span>
        <span class="text-gray-900">My Wallet</span>
    </div>

    {{-- Balance Card --}}
    <div class="relative overflow-hidden mb-8 p-8 rounded-3xl bg-gradient-to-br from-cyan-600 to-cyan-800 shadow-2xl shadow-cyan-700/30 text-white">
        {{-- Decorative rings --}}
        <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-12 -left-8 w-56 h-56 rounded-full bg-white/5"></div>

        <p class="text-sm font-semibold tracking-widest uppercase text-cyan-200 mb-1">Current Balance</p>
        <div class="flex items-end gap-3">
            <span class="text-6xl font-extrabold tracking-tight">{{ number_format($coin_balance) }}</span>
            <span class="mb-2 text-xl font-semibold text-cyan-300">coins</span>
        </div>
        <p class="mt-3 text-sm text-cyan-200">1 coin = ৳1 off your next order.</p>
    </div>

    {{-- Ledger Table --}}
    <div class="overflow-hidden bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Transaction History</h2>
        </div>

        @if($ledger->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-semibold text-gray-700">No transactions yet.</p>
                <p class="mt-1 text-sm text-gray-400">Earn coins by shopping or through promotions.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-gray-400 uppercase">Date</th>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-gray-400 uppercase">Type</th>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-gray-400 uppercase">Description</th>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-gray-400 uppercase text-right">Amount</th>
                            <th class="px-6 py-3 text-xs font-bold tracking-wider text-gray-400 uppercase text-right">Balance After</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ledger as $entry)
                        <tr class="hover:bg-gray-50/40 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700">{{ $entry->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $entry->created_at->format('h:i A') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($entry->type === 'credit')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                        </svg>
                                        Credit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-red-700 bg-red-50 border border-red-200 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                        Debit
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                {{ $entry->description }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold {{ $entry->type === 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $entry->type === 'credit' ? '+' : '−' }}{{ number_format($entry->amount) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($entry->balance_after) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $ledger->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
