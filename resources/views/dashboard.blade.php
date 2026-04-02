@extends('layouts.app')

@section('content')
<div class="container px-4 py-8 mx-auto max-w-7xl" x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 50)">
    
    <div class="mb-6 transition-all duration-700 ease-out transform md:mb-8"
         :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'">
        <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 md:text-3xl">My Dashboard</h1>
        <p class="mt-1 text-sm font-medium text-gray-500">Welcome back, <span class="text-cyan-700">{{ auth()->user()->name }}</span>!</p>
    </div>

    <div class="flex flex-col gap-6 md:flex-row md:gap-8">
        
        <aside class="w-full transition-all duration-700 ease-out delay-100 transform md:w-64 shrink-0"
               :class="mounted ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4'">
            <nav class="flex flex-wrap gap-2 pb-4 md:flex-col md:space-y-2 md:gap-0 md:pb-0">
                
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-bold transition-all duration-300 border shadow-sm rounded-xl text-cyan-700 bg-cyan-50 border-cyan-200 md:py-3 md:font-medium md:text-base">
                    <svg class="w-5 h-5 mr-2 md:mr-3 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Orders & Stats
                </a>

                <a href="{{ route('wishlist.index') }}" class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-300 border border-gray-200 shadow-sm md:border-transparent md:shadow-none bg-white md:bg-transparent rounded-xl text-gray-600 hover:text-cyan-700 hover:bg-gray-50 md:py-3 md:text-base">
                    <svg class="w-5 h-5 mr-2 text-gray-400 md:mr-3 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    My Wishlist
                </a>
                
                <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-300 border border-gray-200 shadow-sm md:border-transparent md:shadow-none bg-white md:bg-transparent rounded-xl text-gray-600 hover:text-cyan-700 hover:bg-gray-50 md:py-3 md:text-base">
                    <svg class="w-5 h-5 mr-2 text-gray-400 md:mr-3 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Saved Addresses
                </a>

                <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium transition-all duration-300 border border-gray-200 shadow-sm md:border-transparent md:shadow-none bg-white md:bg-transparent rounded-xl text-gray-600 hover:text-cyan-700 hover:bg-gray-50 md:py-3 md:text-base">
                    <svg class="w-5 h-5 mr-2 text-gray-400 md:mr-3 group-hover:text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Account Settings
                </a>
            </nav>
        </aside>

        <div class="flex-1 min-w-0 space-y-6 transition-all duration-700 ease-out delay-200 transform"
             :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
            
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
                <div class="relative p-5 overflow-hidden transition-all duration-300 bg-white border border-gray-200 shadow-sm md:p-6 rounded-2xl hover:shadow-lg hover:-translate-y-1 group">
                    <div class="absolute top-0 right-0 p-4 transition-transform duration-500 opacity-10 group-hover:scale-110 group-hover:-rotate-6">
                        <svg class="w-16 h-16 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase md:text-sm">Total Orders</p>
                        <h3 class="mt-2 text-3xl font-extrabold text-gray-900 md:text-4xl">{{ $totalOrders ?? 0 }}</h3>
                    </div>
                </div>

                <div class="relative p-5 transition-all duration-300 bg-white border border-gray-200 shadow-sm md:p-6 rounded-2xl hover:shadow-lg hover:-translate-y-1">
                    <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase md:text-sm">Account Status</p>
                    <div class="flex items-center mt-2 md:mt-3">
                        <div class="relative flex w-3 h-3 mr-3">
                            <span class="absolute inline-flex w-full h-full bg-green-400 rounded-full opacity-75 animate-ping"></span>
                            <span class="relative inline-flex w-3 h-3 bg-green-500 rounded-full"></span>
                        </div>
                        <h3 class="text-xl font-extrabold text-gray-900 md:text-2xl">Active</h3>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden transition-all duration-500 bg-white border border-gray-200 shadow-sm rounded-2xl hover:shadow-md">
                <div class="flex items-center justify-between px-5 py-5 border-b border-gray-100 md:px-6">
                    <h3 class="text-lg font-extrabold text-gray-900">Recent Order History</h3>
                    <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase md:hidden animate-pulse">Swipe &rarr;</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap min-w-[600px]">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-4 text-sm font-bold text-gray-900">Order ID</th>
                                <th class="px-6 py-4 text-sm font-bold text-gray-900">Date</th>
                                <th class="px-6 py-4 text-sm font-bold text-gray-900">Total</th>
                                <th class="px-6 py-4 text-sm font-bold text-gray-900">Status</th>
                                <th class="px-6 py-4 text-sm font-bold text-right text-gray-900">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentOrders as $order)
                                <tr onclick="window.location='{{ route('order.show', $order->id) }}'" class="transition-colors cursor-pointer hover:bg-gray-50 group">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                        #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $order->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                        ৳{{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs font-bold text-orange-700 uppercase bg-orange-100 rounded-full">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                        <a href="{{ route('order.show', $order->id) }}" class="font-bold text-cyan-600 group-hover:text-cyan-800">View Details &rarr;</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        You haven't placed any orders yet. <br>
                                        <a href="{{ route('home') }}" class="inline-block mt-2 font-bold text-cyan-600 hover:underline">Browse Books</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection