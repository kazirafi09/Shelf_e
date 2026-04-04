@extends('layouts.admin')

{{-- 1. Dynamic Header Content --}}
@section('title', 'Order #' . $order->id)
@section('subtitle')
    Placed on <span class="font-bold text-foreground">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</span>
@endsection

@section('admin-content')
<div class="mx-auto max-w-7xl">
    
    {{-- Top Navigation & Actions --}}
    <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between no-print">
        <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-muted-foreground transition hover:text-cyan-600">
            &larr; Back to Order List
        </a>
        <div class="flex flex-wrap items-center gap-3">
            
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-2 pr-4 mr-4 border-r border-border">
                @csrf
                @method('PATCH')
                <label for="status" class="text-xs font-bold tracking-wider text-muted-foreground uppercase">Status:</label>
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-sm font-bold bg-background border border-input text-foreground focus:ring-2 focus:ring-ring focus:outline-none rounded-[var(--radius)] shadow-sm">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>

            <a href="{{ route('admin.orders.invoice', $order->id) }}" 
               class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white transition bg-gray-900 rounded-xl hover:bg-black shadow-lg shadow-gray-900/10 active:scale-95">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                View Invoice
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        
        {{-- LEFT COLUMN: Items & Pricing --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                <div class="px-6 py-5 border-b border-border">
                    <h3 class="text-lg font-black tracking-tight text-foreground uppercase">Items Ordered</h3>
                </div>
                <ul class="divide-y divide-border">
                    @foreach($order->items as $item)
                    <li class="p-6 transition-colors hover:bg-muted/50">
                        <div class="flex items-center space-x-6">
                            <div class="w-16 h-20 overflow-hidden border border-border rounded-lg shadow-sm shrink-0">
                                @if($item->product && $item->product->image_path)
                                    <img src="{{ asset('storage/' . $item->product->image_path) }}" class="object-cover w-full h-full">
                                @else
                                    <div class="flex items-center justify-center w-full h-full text-[10px] font-bold text-muted-foreground bg-muted">NO IMG</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-bold text-foreground truncate">{{ $item->product ? $item->product->title : 'Deleted Product' }}</p>
                                <p class="mt-1 text-sm font-medium text-muted-foreground">Unit Price: ৳ {{ number_format($item->price, 0) }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-muted-foreground">Qty: {{ $item->quantity }}</p>
                                <p class="mt-1 text-base font-black text-foreground">৳ {{ number_format($item->price * $item->quantity, 0) }}</p>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                
                {{-- Pricing Summary Table --}}
                <div class="px-6 py-6 border-t border-border bg-muted/50">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm font-medium text-muted-foreground">
                            <span>Subtotal</span>
                            <span class="text-foreground">৳ {{ number_format($order->subtotal, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-medium text-muted-foreground">
                            <span>Shipping Cost ({{ ucfirst($order->delivery_method) }})</span>
                            <span class="text-foreground">৳ {{ number_format($order->shipping_cost, 0) }}</span>
                        </div>
                        <div class="flex justify-between pt-4 mt-4 border-t border-border">
                            <span class="text-xl font-black tracking-tight text-foreground uppercase">Total Amount</span>
                            <span class="text-2xl font-black text-cyan-600">৳ {{ number_format($order->total_amount, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Customer Details --}}
        <div class="space-y-6">
            <div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl ring-1 ring-gray-900/5">
                <div class="px-6 py-5 border-b border-border">
                    <h3 class="text-lg font-black tracking-tight text-foreground uppercase">Customer Info</h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <p class="mb-1 text-[10px] font-black tracking-widest text-muted-foreground uppercase">Full Name</p>
                        <p class="text-sm font-bold text-foreground">{{ $order->name }}</p>
                    </div>

                    <div>
                        <p class="mb-1 text-[10px] font-black tracking-widest text-muted-foreground uppercase">Email Address</p>
                        <p class="text-sm font-bold text-foreground">
                            <a href="mailto:{{ $order->email }}" class="hover:text-cyan-600 hover:underline">{{ $order->email ?? 'No email provided' }}</a>
                        </p>
                    </div>

                    <div class="pt-4 border-t border-border">
                        <p class="mb-1 text-[10px] font-black tracking-widest text-muted-foreground uppercase">Contact Number</p>
                        <p class="text-sm font-bold text-cyan-600">{{ $order->phone }}</p>
                    </div>
                    <div class="pt-4 border-t border-border">
                        <p class="mb-2 text-[10px] font-black tracking-widest text-muted-foreground uppercase">Shipping Address</p>
                        <div class="text-sm font-medium leading-relaxed text-foreground">
                            <p>{{ $order->address }}</p>
                            <p>{{ $order->district }}, {{ $order->division }}</p>
                            <p class="mt-1 font-bold text-foreground">{{ $order->postal_code }}</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-border">
                        <p class="mb-1 text-[10px] font-black tracking-widest text-muted-foreground uppercase">Payment Method</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-widest bg-orange-50 text-orange-700 border border-orange-100">
                            {{ $order->payment_method }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection