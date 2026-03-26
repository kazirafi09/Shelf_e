@extends('layouts.admin')

@section('content')
<div class="flex min-h-screen font-sans bg-gray-50" x-data="{ sidebarOpen: false }">
    
    <div x-show="sidebarOpen" 
         x-transition.opacity.duration.300ms
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden" style="display: none;"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 h-screen text-white transition-transform duration-300 ease-in-out transform bg-gray-900 shadow-2xl shrink-0 lg:translate-x-0 lg:sticky lg:top-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <div class="flex items-center justify-between p-6 shrink-0">
            <h2 class="text-2xl font-bold tracking-tight"><span class="text-cyan-400">Admin</span>Panel</h2>
            <button @click="sidebarOpen = false" class="text-gray-400 lg:hidden hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex flex-col flex-1 px-4 mt-2 overflow-y-auto">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 font-medium rounded-lg bg-cyan-600 text-cyan-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.books.index') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Manage Books
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Orders
                </a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="pt-10 pb-6 mt-auto">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 font-medium text-left text-red-400 transition rounded-lg hover:bg-gray-800 hover:text-red-300">
                    <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Log Out
                </button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 min-w-0">
        <div class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 lg:hidden">
            <h2 class="text-xl font-bold text-gray-900"><span class="text-cyan-500">Shelf</span>-E Admin</h2>
            <button @click="sidebarOpen = true" class="p-2 -mr-2 text-gray-600 rounded-md hover:bg-gray-100 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>

        <div class="p-4 mx-auto sm:p-6 lg:p-8 max-w-7xl">
            
            <header class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Store Overview</h1>
                    <p class="mt-1 text-sm text-gray-500">Here's what's happening with your store today.</p>
                </div>
                </header>

            <div class="grid grid-cols-1 gap-4 mb-8 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
                
                <div class="relative p-6 overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                    <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">৳ {{ number_format($totalSales, 0) }}</dd>
                    <div class="absolute hidden p-3 bg-cyan-50 rounded-xl right-4 top-4 text-cyan-600 sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="relative p-6 overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                    <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $totalOrders }}</dd>
                    <div class="absolute hidden p-3 text-blue-600 bg-blue-50 rounded-xl right-4 top-4 sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                </div>

                <div class="relative p-6 overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Books in Stock</dt>
                    <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $totalBooks }}</dd>
                    <div class="absolute hidden p-3 text-orange-600 bg-orange-50 rounded-xl right-4 top-4 sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>

                <div class="relative p-6 overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Customers</dt>
                    <dd class="mt-2 text-3xl font-bold tracking-tight text-gray-900">{{ $totalCustomers }}</dd>
                    <div class="absolute hidden p-3 text-purple-600 bg-purple-50 rounded-xl right-4 top-4 sm:block">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                <div class="flex items-center justify-between px-4 py-5 border-b sm:px-6 border-gray-200/60">
                    <h3 class="text-lg font-bold text-gray-900">Recent Transactions</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium transition text-cyan-600 hover:text-cyan-500">View All &rarr;</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Order</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Customer</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Total</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Status</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200/60">
                            @foreach($recentOrders as $order)
                            <tr class="transition duration-150 hover:bg-gray-50/50">
                                <td class="px-4 py-4 font-semibold text-gray-900 sm:px-6">#{{ $order->id }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600 sm:px-6">{{ $order->name ?? 'Guest' }}</td>
                                <td class="px-4 py-4 font-semibold text-gray-900 sm:px-6">৳ {{ number_format($order->total_amount, 0) }}</td>
                                <td class="px-4 py-4 sm:px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 sm:px-6">{{ $order->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if(count($recentOrders) === 0)
                <div class="px-6 py-8 text-sm text-center text-gray-500">
                    No recent transactions found.
                </div>
                @endif
            </div>

        </div>
    </main>
</div>
@endsection