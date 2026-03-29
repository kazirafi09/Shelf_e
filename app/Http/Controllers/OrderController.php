<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect('/categories')->with('error', 'Your cart is empty!');
        }

        // FIX 1.6: Better validation rules to prevent excessively large string inputs
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|min:10|max:20',
            'address' => 'required|string|max:500',
            'division' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'delivery' => 'required|string',
            'payment' => 'required|string',
        ]);

        // Optimized subtotal calculation
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = ($request->delivery === 'express') ? 150 : 60;
        $total = $subtotal + $shipping;

        // FIX 1.6: Strip all non-numeric characters to prevent injection/formatting errors
        $cleanPhone = preg_replace('/[^0-9]/', '', $validatedData['phone']);
        $standardizedPhone = '+880' . ltrim($cleanPhone, '0');

        // Use a Transaction to ensure everything saves or nothing saves
        DB::transaction(function () use ($cart, $validatedData, $subtotal, $shipping, $total, $standardizedPhone) {
            
            $order = Order::create([
                'user_id' => auth()->id(),
                'name' => $validatedData['name'] ?? (auth()->check() ? auth()->user()->name : 'Guest'),
                'phone' => $standardizedPhone, 
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

            // FIX 3.1: Bulk Insert Order Items (1 query instead of N queries)
            $orderItems = collect($cart)->map(function ($item, $id) use ($order) {
                return [
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            })->values()->toArray();
            
            OrderItem::insert($orderItems);

            // FIX 3.1: Bulk Update Stock Quantities using parameterized CASE statement
            // This is the fastest, safest way to update multiple rows in a single database hit
            $cases = [];
            $params = [];
            $ids = [];
            
            foreach ($cart as $id => $item) {
                $cases[] = "WHEN id = ? THEN stock_quantity - ?";
                $params[] = $id;
                $params[] = $item['quantity'];
                $ids[] = $id;
            }
            
            $idsPlaceholders = implode(',', array_fill(0, count($ids), '?'));
            $params = array_merge($params, $ids);
            $casesSql = implode(' ', $cases);
            
            DB::update("UPDATE products SET stock_quantity = CASE {$casesSql} ELSE stock_quantity END WHERE id IN ({$idsPlaceholders})", $params);
        });

        session()->forget('cart');

        return redirect('/')->with('success', 'Order placed successfully! Check your dashboard for updates.');
    }
}