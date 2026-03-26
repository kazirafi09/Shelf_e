@extends('layouts.admin')

@section('title', 'Store Overview')
@section('subtitle', "Here's what's happening with your store today.")

@section('admin-content')
    {{-- Statistics Grid --}}
    <div class="grid grid-cols-1 gap-4 mb-8 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
        
        <div class="relative p-6 transition-all bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5 group hover:ring-cyan-500/50">
            <dt class="text-sm font-bold tracking-wider text-gray-500 uppercase">Total Revenue</dt>
            <dd class="mt-2 text-3xl font-black tracking-tight text-gray-900">৳ {{ number_format($totalSales, 0) }}</dd>
            <div class="absolute hidden p-3 bg-cyan-50 rounded-xl right-4 top-4 text-cyan-600 sm:block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="relative p-6 transition-all bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5 group hover:ring-blue-500/50">
            <dt class="text-sm font-bold tracking-wider text-gray-500 uppercase">Total Orders</dt>
            <dd class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ $totalOrders }}</dd>
            <div class="absolute hidden p-3 text-blue-600 bg-blue-50 rounded-xl right-4 top-4 sm:block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
        </div>

        <div class="relative p-6 transition-all bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5 group hover:ring-orange-500/50">
            <dt class="text-sm font-bold tracking-wider text-gray-500 uppercase">Books in Stock</dt>
            <dd class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ $totalBooks }}</dd>
            <div class="absolute hidden p-3 text-orange-600 bg-orange-50 rounded-xl right-4 top-4 sm:block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>

        <div class="relative p-6 transition-all bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5 group hover:ring-purple-500/50">
            <dt class="text-sm font-bold tracking-wider text-gray-500 uppercase">Total Customers</dt>
            <dd class="mt-2 text-3xl font-black tracking-tight text-gray-900">{{ $totalCustomers }}</dd>
            <div class="absolute hidden p-3 text-purple-600 bg-purple-50 rounded-xl right-4 top-4 sm:block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Recent Transactions Table --}}
    <div class="overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-black tracking-tight text-gray-900 uppercase">Recent Transactions</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold transition-colors text-cyan-600 hover:text-cyan-700">View All &rarr;</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Order</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Customer</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Total</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-400 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders as $order)
                    <tr class="transition duration-150 hover:bg-gray-50/50 group">
                        <td class="px-6 py-4 font-bold text-gray-900 transition-colors group-hover:text-cyan-600">#{{ $order->id }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $order->name ?? 'Guest User' }}</td>
                        <td class="px-6 py-4 font-black text-gray-900">৳ {{ number_format($order->total_amount, 0) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 italic font-medium text-center text-gray-400">
                            No transactions recorded today.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection