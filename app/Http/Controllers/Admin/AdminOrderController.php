<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminOrderController extends Controller
{
    // 1. Display all orders
    public function index()
    {
        // FIX B-3: Added eager loading for the user to prevent massive N+1 queries
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    // 2. Update the status of a specific order
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // FIX A-5: Require explicit policy authorization before modifying
        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Order #{$order->id} status updated to " . ucfirst($request->status) . ".");
    }

    // Show single order details
    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // Generate printable invoice
    public function invoice($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }
}
