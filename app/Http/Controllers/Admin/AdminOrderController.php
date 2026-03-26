<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order; // Make sure your Order model is imported!

class AdminOrderController extends Controller
{
    // 1. Display all orders
    public function index()
    {
        // Fetch all orders, newest first, 15 per page
        $orders = Order::orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.orders.index', compact('orders'));
    }

    // 2. Update the status of a specific order
    public function updateStatus(Request $request, $id)
    {
        // Ensure they only pass valid statuses
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', "Order #{$order->id} status updated to " . ucfirst($request->status) . ".");
    }
    // Show single order details
    public function show($id)
    {
        // Eager load the items and the associated products to prevent N+1 queries
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