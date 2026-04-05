@extends('layouts.dashboard')

@section('page-title', 'My Dashboard')

@section('dashboard-content')

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
    <div class="relative p-5 overflow-hidden transition-all duration-300 bg-card text-card-foreground border border-border shadow-sm md:p-6 rounded-2xl hover:shadow-lg hover:-translate-y-1 group">
        <div class="absolute top-0 right-0 p-4 transition-transform duration-500 opacity-10 group-hover:scale-110 group-hover:-rotate-6">
            <svg class="w-16 h-16 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <div class="relative z-10">
            <p class="text-xs font-semibold tracking-wider text-muted-foreground uppercase md:text-sm">Total Orders</p>
            <h3 class="mt-2 text-3xl font-extrabold text-foreground md:text-4xl">{{ $totalOrders ?? 0 }}</h3>
        </div>
    </div>

    <div class="relative p-5 transition-all duration-300 bg-card text-card-foreground border border-border shadow-sm md:p-6 rounded-2xl hover:shadow-lg hover:-translate-y-1">
        <p class="text-xs font-semibold tracking-wider text-muted-foreground uppercase md:text-sm">Account Status</p>
        <div class="flex items-center mt-2 md:mt-3">
            <div class="relative flex w-3 h-3 mr-3">
                <span class="absolute inline-flex w-full h-full bg-green-400 rounded-full opacity-75 animate-ping"></span>
                <span class="relative inline-flex w-3 h-3 bg-green-500 rounded-full"></span>
            </div>
            <h3 class="text-xl font-extrabold text-foreground md:text-2xl">Active</h3>
        </div>
    </div>
</div>

<div class="overflow-hidden transition-all duration-500 bg-card text-card-foreground border border-border shadow-sm rounded-2xl hover:shadow-md">
    <div class="flex items-center justify-between px-5 py-5 border-b border-border md:px-6">
        <h3 class="text-lg font-extrabold text-foreground">Recent Order History</h3>
        <span class="text-[10px] font-bold tracking-widest text-muted-foreground uppercase md:hidden animate-pulse">Swipe &rarr;</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap min-w-[600px]">
            <thead class="bg-muted">
                <tr>
                    <th class="px-6 py-4 text-sm font-bold text-foreground">Order ID</th>
                    <th class="px-6 py-4 text-sm font-bold text-foreground">Date</th>
                    <th class="px-6 py-4 text-sm font-bold text-foreground">Total</th>
                    <th class="px-6 py-4 text-sm font-bold text-foreground">Status</th>
                    <th class="px-6 py-4 text-sm font-bold text-right text-foreground">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($recentOrders as $order)
                    <tr onclick="window.location='{{ route('order.show', $order->id) }}'" class="transition-colors cursor-pointer hover:bg-muted group">
                        <td class="px-6 py-4 text-sm font-medium text-foreground whitespace-nowrap">
                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-muted-foreground whitespace-nowrap">
                            {{ $order->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-foreground whitespace-nowrap">
                            ৳{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-bold text-gray-800 uppercase bg-gray-100 rounded-full">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                            <a href="{{ route('order.show', $order->id) }}" class="font-bold text-gray-700 group-hover:text-gray-900">View Details &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-muted-foreground">
                            You haven't placed any orders yet. <br>
                            <a href="{{ route('home') }}" class="inline-block mt-2 font-bold text-gray-700 hover:underline">Browse Books</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
