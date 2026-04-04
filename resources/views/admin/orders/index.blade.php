@extends('layouts.admin')

@section('title', 'Manage Orders')
@section('subtitle', 'View and update customer order statuses.')

@section('admin-content')
    {{-- Alert Message --}}
    @if(session('success'))
        <div class="p-4 mb-6 text-sm font-bold text-green-700 bg-green-100 border border-green-200 rounded-xl animate-pulse">
            <span class="mr-2">📦</span> {{ session('success') }}
        </div>
    @endif

    {{-- Orders Table --}}
    <div class="overflow-hidden bg-card text-card-foreground border border-border shadow-sm rounded-2xl ring-1 ring-gray-900/5">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-muted">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Order ID</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Customer</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Date</th>
                        <th class="px-6 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase">Status Action</th>
                    </tr>
                </thead>
                <tbody class="bg-card divide-y divide-border">
                    @forelse($orders as $order)
                    <tr class="transition duration-150 hover:bg-muted/30 group">
                        <td class="px-6 py-4 font-bold text-foreground transition-colors group-hover:text-cyan-600">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-muted-foreground">
                            {{ $order->name ?? 'Guest User' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-foreground">
                            ৳ {{ number_format($order->total_amount, 0) }}
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-muted-foreground">
                            {{ $order->created_at->format('M d, Y') }}
                            <span class="block text-[10px] text-muted-foreground">{{ $order->created_at->format('h:ia') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                {{-- Smart Status Dropdown --}}
                                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="w-36">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" 
                                            class="block w-full py-1.5 text-[11px] font-black uppercase tracking-widest rounded-lg shadow-sm bg-background border border-input focus:ring-2 focus:ring-ring focus:outline-none transition-all cursor-pointer
                                            @if($order->status == 'pending') bg-yellow-50 text-yellow-700
                                            @elseif($order->status == 'processing') bg-purple-50 text-purple-700
                                            @elseif($order->status == 'shipped') bg-blue-50 text-blue-700
                                            @elseif($order->status == 'delivered') bg-green-50 text-green-700
                                            @elseif($order->status == 'cancelled') bg-red-50 text-red-700
                                            @endif">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>

                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="inline-flex items-center justify-center p-2 transition rounded-lg text-cyan-600 bg-cyan-50 hover:bg-cyan-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <p class="font-medium text-muted-foreground">No customer orders yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
@endsection