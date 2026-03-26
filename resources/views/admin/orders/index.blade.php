@extends('layouts.admin')

@section('content')
<div class="flex min-h-screen font-sans bg-gray-50" x-data="{ sidebarOpen: false }">
    
    <div x-show="sidebarOpen" x-transition.opacity.duration.300ms @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden" style="display: none;"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 h-screen text-white transition-transform duration-300 ease-in-out transform bg-gray-900 shadow-2xl shrink-0 lg:translate-x-0 lg:sticky lg:top-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
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
        <div class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 lg:hidden">
            <h2 class="text-xl font-bold text-gray-900"><span class="text-cyan-500">Shelf</span>-E Admin</h2>
            <button @click="sidebarOpen = true" class="p-2 -mr-2 text-gray-600 rounded-md hover:bg-gray-100"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
        </div>

        <div class="p-4 mx-auto sm:p-6 lg:p-8 max-w-7xl">
            
            <header class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Manage Orders</h1>
                    <p class="mt-1 text-sm text-gray-500">View and update customer order statuses.</p>
                </div>
            </header>

            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    <span class="font-medium">Success!</span> {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Order ID</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Customer</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Amount</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Date</th>
                                <th class="px-4 py-4 text-xs font-semibold tracking-wider text-gray-500 uppercase sm:px-6">Status Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200/60">
                            @forelse($orders as $order)
                            <tr class="transition duration-150 hover:bg-gray-50/50">
                                <td class="px-4 py-4 font-semibold text-gray-900 sm:px-6">#{{ $order->id }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600 sm:px-6">{{ $order->name ?? 'Guest' }}</td>
                                <td class="px-4 py-4 font-semibold text-gray-900 sm:px-6">৳ {{ number_format($order->total_amount, 0) }}</td>
                                <td class="px-4 py-4 text-sm text-gray-500 sm:px-6">{{ $order->created_at->format('M d, Y h:ia') }}</td>
                                <td class="px-4 py-4 sm:px-6">
                                <div class="flex items-center space-x-4">
                                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="w-32">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" @change="$el.form.submit()" class="block w-full py-1 text-xs border-gray-300 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500
                                            @if($order->status == 'pending') bg-yellow-50 text-yellow-800 border-yellow-200
                                            @elseif($order->status == 'processing') bg-purple-50 text-purple-800 border-purple-200
                                            @elseif($order->status == 'shipped') bg-blue-50 text-blue-800 border-blue-200
                                            @elseif($order->status == 'delivered') bg-green-50 text-green-800 border-green-200
                                            @elseif($order->status == 'cancelled') bg-red-50 text-red-800 border-red-200
                                            @endif">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>

                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-medium transition text-cyan-600 hover:text-cyan-900">
                                        View Details &rarr;
                                    </a>
                                </div>
                            </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-500 sm:px-6">
                                    No orders found in the system yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>

        </div>
    </main>
</div>
@endsection