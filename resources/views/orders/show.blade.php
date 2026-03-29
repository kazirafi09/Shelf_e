@extends('layouts.app')

@section('content')
<div class="container max-w-5xl px-4 py-8 mx-auto">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-cyan-600">&larr; Back to Dashboard</a>
            <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-gray-600">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>
        <span class="px-4 py-2 text-sm font-bold text-orange-700 uppercase bg-orange-100 rounded-full">
            Status: {{ $order->status }}
        </span>
    </div>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
        <div class="p-6 bg-white border border-gray-200 shadow-sm md:col-span-2 rounded-2xl">
            <h3 class="pb-3 mb-4 text-lg font-bold text-gray-900 border-b border-gray-100">Items Ordered</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center justify-center w-12 h-16 bg-gray-100 rounded shrink-0">
                            @if($item->product && $item->product->image_path)
                                <img src="{{ asset('storage/' . $item->product->image_path) }}" class="object-cover w-full h-full rounded" alt="{{ $item->product->title }}">
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $item->product ? $item->product->title : 'Product no longer available' }}</h4>
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} x ৳{{ number_format($item->price, 2) }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-900">৳{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="p-6 border border-gray-200 bg-gray-50 rounded-2xl">
                <h3 class="pb-3 mb-4 text-lg font-bold text-gray-900 border-b border-gray-200">Summary</h3>
                <div class="flex justify-between mb-2 text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>৳{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2 text-sm text-gray-600">
                    <span>Shipping ({{ ucfirst($order->delivery_method) }})</span>
                    <span>৳{{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between pt-3 mt-3 font-bold text-gray-900 border-t border-gray-200">
                    <span>Total</span>
                    <span class="text-cyan-600">৳{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-2xl">
                <h3 class="pb-3 mb-4 text-lg font-bold text-gray-900 border-b border-gray-100">Delivery Address</h3>
                <address class="space-y-1 text-sm not-italic text-gray-600">
                    <p class="font-bold text-gray-900">{{ $order->name }}</p>
                    <p>{{ $order->phone }}</p>
                    <p class="mt-2">{{ $order->address }}</p>
                    <p>{{ $order->district }}, {{ $order->division }} {{ $order->postal_code }}</p>
                </address>
            </div>
        </div>
    </div>
</div>
@endsection