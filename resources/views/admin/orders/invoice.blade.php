@extends('layouts.admin')

@section('title', 'Order Invoice')
@section('subtitle', 'Print-ready document for Order #' . $order->id)

@section('admin-content')
<style>
    /* High-fidelity print settings */
    @media print {
        body { background-color: white !important; }
        .no-print { display: none !important; }
        aside { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; }
        .print-container { 
            border: none !important; 
            box-shadow: none !important; 
            padding: 0 !important; 
            max-width: 100% !important; 
            margin: 0 !important;
        }
    }
</style>

<div class="max-w-4xl mx-auto">
    
    {{-- Top Action Bar (Hidden on Print) --}}
    <div class="flex items-center justify-between mt-4 mb-8 no-print">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-bold text-gray-500 transition hover:text-cyan-600">
            &larr; Back to Order Details
        </a>
        <button onclick="window.print()" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition rounded-xl shadow-lg bg-cyan-600 hover:bg-cyan-700 shadow-cyan-600/20 active:scale-95">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Invoice
        </button>
    </div>

    {{-- The Invoice Paper --}}
    <div class="p-12 bg-white border border-gray-100 shadow-2xl print-container rounded-3xl">
        
        {{-- Invoice Header --}}
        <div class="flex items-start justify-between pb-10 mb-10 border-b-2 border-gray-100">
            <div>
                <h1 class="mb-1 text-4xl font-black tracking-tighter text-gray-900"><span class="text-cyan-500">Shelf</span>-E</h1>
                <p class="text-sm font-bold tracking-widest text-gray-400 uppercase">Premium Bookstore</p>
            </div>
            <div class="text-sm text-right">
                <p class="mb-2 text-2xl font-black tracking-tight text-gray-900">INVOICE</p>
                <div class="space-y-1 text-gray-500">
                    <p><span class="font-bold text-gray-900">Order ID:</span> #{{ $order->id }}</p>
                    <p><span class="font-bold text-gray-900">Date:</span> {{ $order->created_at->format('M d, Y') }}</p>
                    <p><span class="font-bold text-gray-900">Payment:</span> {{ strtoupper($order->payment_method) }}</p>
                </div>
            </div>
        </div>

        {{-- Address Section --}}
        <div class="grid grid-cols-2 gap-12 mb-12">
            <div class="text-sm">
                <p class="mb-3 text-[10px] font-black tracking-widest text-gray-400 uppercase">From</p>
                <p class="text-lg font-bold text-gray-900">Shelf-E Fulfillment</p>
                <div class="mt-2 leading-relaxed text-gray-600">
                    <p>123 Bookworm Lane</p>
                    <p>Dhaka, Bangladesh</p>
                    <p class="mt-2 font-bold text-gray-900">+880 1234 567890</p>
                </div>
            </div>
            <div class="text-sm">
                <p class="mb-3 text-[10px] font-black tracking-widest text-gray-400 uppercase">Ship To</p>
                <p class="text-lg font-bold text-gray-900">{{ $order->name }}</p>
                <div class="mt-2 leading-relaxed text-gray-600">
                    <p>{{ $order->address }}</p>
                    <p>{{ $order->district }}, {{ $order->division }} {{ $order->postal_code }}</p>
                    <p class="mt-2 font-bold text-gray-900">Phone: {{ $order->phone }}</p>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <table class="w-full mb-10 text-left border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-900">
                    <th class="py-4 text-[10px] font-black tracking-widest text-gray-900 uppercase">Item Description</th>
                    <th class="py-4 text-[10px] font-black tracking-widest text-center text-gray-900 uppercase">Qty</th>
                    <th class="py-4 text-[10px] font-black tracking-widest text-right text-gray-900 uppercase">Unit Price</th>
                    <th class="py-4 text-[10px] font-black tracking-widest text-right text-gray-900 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                <tr>
                    <td class="py-5 font-bold text-gray-900">{{ $item->product ? $item->product->title : 'Untitled Book' }}</td>
                    <td class="py-5 font-medium text-center text-gray-600">{{ $item->quantity }}</td>
                    <td class="py-5 font-medium text-right text-gray-600">৳ {{ number_format($item->price, 0) }}</td>
                    <td class="py-5 font-black text-right text-gray-900">৳ {{ number_format($item->price * $item->quantity, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals Section --}}
        <div class="flex justify-end pt-6 border-t-2 border-gray-900">
            <div class="w-full max-w-[280px]">
                <div class="flex justify-between py-2 text-sm font-medium text-gray-500">
                    <span>Subtotal</span>
                    <span class="text-gray-900">৳ {{ number_format($order->subtotal, 0) }}</span>
                </div>
                <div class="flex justify-between py-2 text-sm font-medium text-gray-500">
                    <span>Shipping ({{ ucfirst($order->delivery_method) }})</span>
                    <span class="text-gray-900">৳ {{ number_format($order->shipping_cost, 0) }}</span>
                </div>
                <div class="flex justify-between py-4 mt-2 text-xl font-black border-t border-gray-100 text-cyan-600">
                    <span>Grand Total</span>
                    <span>৳ {{ number_format($order->total_amount, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="pt-12 mt-16 text-center border-t border-gray-100">
            <p class="text-sm font-black tracking-tight text-gray-900">Thank you for your purchase!</p>
            <p class="mt-2 text-xs leading-relaxed text-gray-400">
                Returns accepted within 7 days in original condition. <br>
                For support, email us at <span class="font-bold text-cyan-600">support@shelf-e.com</span>
            </p>
        </div>

    </div>
</div>
@endsection