@extends('layouts.admin')

@section('content')
<div class="flex min-h-screen font-sans bg-gray-50" x-data="{ sidebarOpen: false }">
    
    <div x-show="sidebarOpen" x-transition.opacity.duration.300ms @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden print:hidden" style="display: none;"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 h-screen text-white transition-transform duration-300 ease-in-out transform -translate-x-full bg-gray-900 shadow-2xl shrink-0 lg:translate-x-0 lg:sticky lg:top-0 print:hidden" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex items-center justify-between p-6 shrink-0">
            <h2 class="text-2xl font-bold tracking-tight"><span class="text-cyan-400">Admin</span>Panel</h2>
            <button @click="sidebarOpen = false" class="text-gray-400 lg:hidden hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <nav class="flex flex-col flex-1 px-4 mt-2 overflow-y-auto">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg> Dashboard
                </a>
                <a href="{{ route('admin.books.index') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> Manage Books
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 font-medium rounded-lg bg-cyan-600 text-cyan-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg> Orders
                </a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="pt-10 pb-6 mt-auto">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 font-medium text-left text-red-400 transition rounded-lg hover:bg-gray-800 hover:text-red-300"><svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg> Log Out</button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 min-w-0">
        <div class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 lg:hidden print:hidden">
            <h2 class="text-xl font-bold text-gray-900"><span class="text-cyan-500">Shelf</span>-E Admin</h2>
            <button @click="sidebarOpen = true" class="p-2 -mr-2 text-gray-600 rounded-md hover:bg-gray-100"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
        </div>

        <div class="p-4 mx-auto sm:p-6 lg:p-8 max-w-7xl">
            <header class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-900">&larr; Back to Orders</a>
                    <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Order #{{ $order->id }}</h1>
                    <p class="mt-1 text-sm text-gray-500">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                <div class="flex gap-3 shrink-0">
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white transition bg-gray-900 rounded-lg hover:bg-black shadow-sm shrink-0">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Receipt
                    </a>
                </div>
            </header>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg font-bold text-gray-900">Books Ordered</h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                            <li class="p-4 sm:px-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-16 overflow-hidden bg-gray-100 border border-gray-200 rounded shrink-0">
                                        @if($item->product && $item->product->image_path)
                                            <img src="{{ asset('storage/' . $item->product->image_path) }}" class="object-cover w-full h-full">
                                        @else
                                            <div class="flex items-center justify-center w-full h-full text-[10px] text-gray-400">Cover</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $item->product ? $item->product->title : 'Unknown Book' }}</p>
                                        <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="font-medium text-gray-900">
                                        ৳ {{ number_format($item->price * $item->quantity, 0) }}
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        <div class="px-4 py-5 border-t border-gray-200 bg-gray-50 sm:px-6">
                            <div class="flex justify-between mb-2 text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium text-gray-900">৳ {{ number_format($order->subtotal, 0) }}</span>
                            </div>
                            <div class="flex justify-between mb-4 text-sm">
                                <span class="text-gray-500">Shipping ({{ ucfirst($order->delivery_method) }})</span>
                                <span class="font-medium text-gray-900">৳ {{ number_format($order->shipping_cost, 0) }}</span>
                            </div>
                            <div class="flex justify-between pt-4 text-lg font-bold border-t border-gray-200">
                                <span>Total</span>
                                <span class="text-orange-600">৳ {{ number_format($order->total_amount, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg font-bold text-gray-900">Customer & Delivery Info</h3>
                        </div>
                        <div class="px-4 py-5 space-y-4 sm:px-6">
                            <div>
                                <p class="mb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">Customer Name</p>
                                <p class="text-sm font-medium text-gray-900">{{ $order->name }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">Contact Phone</p>
                                <p class="text-sm font-medium text-gray-900">{{ $order->phone }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">Shipping Address</p>
                                <p class="text-sm text-gray-900">{{ $order->address }}</p>
                                <p class="text-sm text-gray-900">{{ $order->district }}, {{ $order->division }} {{ $order->postal_code }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection