@extends('layouts.admin')

@section('content')
<style>
    /* Force page breaks and hide UI elements when printing */
    @media print {
        body { background-color: white !important; }
        .no-print { display: none !important; }
        .print-container { border: none !important; box-shadow: none !important; padding: 0 !important; max-width: 100% !important; margin: 0 !important;}
    }
</style>

<div class="flex min-h-screen font-sans bg-gray-50" x-data="{ sidebarOpen: false }">
    
    <div x-show="sidebarOpen" x-transition.opacity.duration.300ms @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm lg:hidden print:hidden" style="display: none;"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 h-screen text-white transition-transform duration-300 ease-in-out transform -translate-x-full bg-gray-900 shadow-2xl shrink-0 lg:translate-x-0 lg:sticky lg:top-0 print:hidden" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex items-center justify-between p-6 shrink-0">
            <h2 class="text-2xl font-bold tracking-tight"><span class="text-cyan-400">Admin</span>Panel</h2>
            <button @click="sidebarOpen = false" class="text-gray-400 lg:hidden hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <nav class="flex flex-col flex-1 px-4 mt-2 overflow-y-auto">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">Dashboard</a>
                <a href="{{ route('admin.books.index') }}" class="flex items-center px-4 py-3 font-medium text-gray-400 transition rounded-lg hover:bg-gray-800 hover:text-white">Manage Books</a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 font-medium rounded-lg bg-cyan-600 text-cyan-50">Orders</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="pt-10 pb-6 mt-auto">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-3 font-medium text-left text-red-400 transition rounded-lg hover:bg-gray-800 hover:text-red-300">Log Out</button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 min-w-0">
        <div class="flex items-center justify-between px-4 py-4 bg-white border-b border-gray-200 lg:hidden print:hidden">
            <h2 class="text-xl font-bold text-gray-900"><span class="text-cyan-500">Shelf</span>-E Admin</h2>
            <button @click="sidebarOpen = true" class="p-2 -mr-2 text-gray-600 rounded-md hover:bg-gray-100"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
        </div>

        <div class="p-4 mx-auto sm:p-6 lg:p-8">
            
            <div class="flex items-center justify-between max-w-3xl mx-auto mt-4 mb-6 no-print">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-900">&larr; Back to Order Details</a>
                <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition rounded-lg shadow-sm bg-cyan-600 hover:bg-cyan-700">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Document
                </button>
            </div>

            <div class="max-w-3xl p-10 mx-auto bg-white border border-gray-200 shadow-sm print-container rounded-xl">
                
                <div class="flex items-start justify-between pb-8 mb-8 border-b border-gray-200">
                    <div>
                        <h1 class="mb-1 text-3xl font-extrabold text-gray-900"><span class="text-cyan-500">Shelf</span>-E</h1>
                        <p class="text-sm text-gray-500">Your Favorite Bookstore</p>
                    </div>
                    <div class="text-sm text-right text-gray-500">
                        <p class="mb-1 text-lg font-bold text-gray-900">INVOICE</p>
                        <p>Order ID: #{{ $order->id }}</p>
                        <p>Date: {{ $order->created_at->format('M d, Y') }}</p>
                        <p>Payment: {{ strtoupper($order->payment_method) }}</p>
                    </div>
                </div>

                <div class="flex justify-between mb-8">
                    <div class="text-sm">
                        <p class="mb-2 text-xs font-bold tracking-wider text-gray-900 uppercase">From</p>
                        <p class="font-bold">Shelf-E Fulfillment</p>
                        <p class="text-gray-600">123 Bookworm Lane</p>
                        <p class="text-gray-600">Dhaka, Bangladesh</p>
                        <p class="text-gray-600">+880 1234 567890</p>
                    </div>
                    <div class="text-sm">
                        <p class="mb-2 text-xs font-bold tracking-wider text-gray-900 uppercase">Ship To</p>
                        <p class="font-bold text-gray-900">{{ $order->name }}</p>
                        <p class="text-gray-600">{{ $order->address }}</p>
                        <p class="text-gray-600">{{ $order->district }}, {{ $order->division }} {{ $order->postal_code }}</p>
                        <p class="mt-1 font-medium text-gray-900">Phone: {{ $order->phone }}</p>
                    </div>
                </div>

                <table class="w-full mb-8 text-left">
                    <thead class="border-b-2 border-gray-800">
                        <tr>
                            <th class="py-3 text-xs font-bold text-gray-800 uppercase">Item Description</th>
                            <th class="py-3 text-xs font-bold text-center text-gray-800 uppercase">Qty</th>
                            <th class="py-3 text-xs font-bold text-right text-gray-800 uppercase">Unit Price</th>
                            <th class="py-3 text-xs font-bold text-right text-gray-800 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-4 font-medium text-gray-900">{{ $item->product ? $item->product->title : 'Book Title' }}</td>
                            <td class="py-4 text-center text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-4 text-right text-gray-600">৳ {{ number_format($item->price, 0) }}</td>
                            <td class="py-4 font-bold text-right text-gray-900">৳ {{ number_format($item->price * $item->quantity, 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end">
                    <div class="w-1/2 text-sm">
                        <div class="flex justify-between py-2 text-gray-600">
                            <span>Subtotal</span>
                            <span>৳ {{ number_format($order->subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-gray-600 border-b border-gray-200">
                            <span>Shipping ({{ ucfirst($order->delivery_method) }})</span>
                            <span>৳ {{ number_format($order->shipping_cost, 0) }}</span>
                        </div>
                        <div class="flex justify-between py-3 text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>৳ {{ number_format($order->total_amount, 0) }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-8 mt-12 text-xs text-center text-gray-500 border-t border-gray-200">
                    <p class="mb-1 font-bold text-gray-800">Thank you for shopping with Shelf-E!</p>
                    <p>If you have any questions regarding this invoice, please contact support@shelf-e.com.</p>
                    <p class="mt-2">Books can be returned within 7 days if they are in original condition.</p>
                </div>

            </div>
        </div>
    </main>
</div>
@endsection