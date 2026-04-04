@extends('layouts.app')

@section('content')
<div class="container max-w-3xl px-4 py-16 mx-auto">
    <div class="p-8 text-center bg-card text-card-foreground border border-border shadow-xl rounded-3xl sm:p-12">
        
        <div class="inline-flex items-center justify-center w-20 h-20 mb-6 text-green-500 rounded-full bg-green-50 ring-8 ring-green-50">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="mb-2 text-4xl font-extrabold text-foreground">Order Confirmed!</h1>
        <p class="mb-8 text-lg text-muted-foreground">Thank you for your purchase. We're getting your books ready for shipment.</p>

        <div class="p-6 mb-8 text-left border border-border bg-muted rounded-2xl">
            <h3 class="pb-2 mb-4 text-lg font-bold text-foreground border-b border-border">Order Details</h3>

            <div class="flex justify-between mb-2">
                <span class="text-muted-foreground">Order Number:</span>
                <span class="font-bold text-foreground">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-muted-foreground">Date:</span>
                <span class="font-medium text-foreground">{{ $order->created_at->format('M j, Y') }}</span>
            </div>
            <div class="flex justify-between pt-2 mt-2 border-t border-border">
                <span class="font-bold text-foreground">Total Paid:</span>
                <span class="font-bold text-cyan-600">৳{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-3 font-bold text-white transition-colors shadow-md bg-cyan-600 rounded-xl hover:bg-cyan-700">
                    View My Orders
                </a>
            @endauth
            <a href="{{ route('home') }}" class="px-6 py-3 font-bold text-foreground transition-colors bg-background border border-border shadow-sm rounded-xl hover:bg-muted">
                Continue Shopping
            </a>
        </div>

    </div>
</div>
@endsection