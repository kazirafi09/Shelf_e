<?php
 
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/categories')->with('error', 'Your cart is empty!');
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'division' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'nullable|string|max:10',
            'delivery' => 'required|string',
            'payment' => 'required|string',
        ]);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $shipping = ($request->delivery === 'express') ? 150 : 60;
        $total = $subtotal + $shipping;

        // Use a Transaction to ensure everything saves or nothing saves
        DB::transaction(function () use ($cart, $validatedData, $subtotal, $shipping, $total) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'name' => $validatedData['name'] ?? (auth()->check() ? auth()->user()->name : 'Guest'),
                'phone' => '+880' . ltrim($validatedData['phone'], '0'), 
                'address' => $validatedData['address'],
                'division' => $validatedData['division'],
                'district' => $validatedData['district'],
                'postal_code' => $validatedData['postal_code'],
                'delivery_method' => $validatedData['delivery'],
                'payment_method' => $validatedData['payment'],
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            foreach ($cart as $id => $item) {
                // 1. Save the item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // 2. Reduce Stock
                Product::where('id', $id)->decrement('stock_quantity', $item['quantity']);
            }
        });

        session()->forget('cart');

        return redirect('/')->with('success', 'Order placed successfully! Check your dashboard for updates.');
    }
}