<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientCoinsException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use App\Services\CoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct(private readonly CoinService $coinService) {}

    public function index()
    {
        $cartItems = session()->get('cart', []);
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = count($cartItems) > 0 ? 150 : 0;
        $total = $subtotal + $shipping;

        // Pre-fill from the user's most recent order (returning customers)
        $lastOrder = null;
        if (auth()->check()) {
            $lastOrder = Order::where('user_id', auth()->id())
                ->latest()
                ->first(['name', 'email', 'phone', 'address', 'division', 'district', 'postal_code']);
        }

        $savedAddresses = auth()->check()
            ? UserAddress::where('user_id', auth()->id())->orderByDesc('is_default')->orderBy('created_at')->get()
            : collect();

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total', 'lastOrder', 'savedAddresses'));
    }

    public function show($id)
    {
        // Eager load items and products
        $order = Order::with('items.product')->findOrFail($id);

        // Strict IDOR protection: only the owner can view this
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access. You can only view your own orders.');
        }

        return view('orders.show', compact('order'));
    }
    public function confirmation($id)
    {
        // Eager load the items and associated products to prevent N+1 queries
        $order = \App\Models\Order::with('items.product')->findOrFail($id);

        // Security check: Ensure the user can only view their own order
        if (auth()->check() && $order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        return view('checkout.confirmation', compact('order'));
    }
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/categories')->with('error', 'Your cart is empty!');
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:10|max:20',
            'address' => 'required|string|max:500',
            'division' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'delivery'     => 'required|string',
            'payment'      => 'required|string',
            'redeem_coins' => 'nullable|boolean',
        ]);

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $shipping = ($request->delivery === 'express') ? 150 : 60;
        $total = $subtotal + $shipping;

        $user = auth()->user();
        $coinsToRedeem = 0;
        if ($request->boolean('redeem_coins') && $user && $user->coin_balance > 0) {
            $coinsToRedeem = min($user->coin_balance, (int) $total);
        }
        $finalTotal = $total - $coinsToRedeem;

        $cleanPhone = preg_replace('/[^0-9]/', '', $validatedData['phone']);
        $standardizedPhone = '+880' . ltrim($cleanPhone, '0');

        // NOTICE THIS LINE: We are capturing the output of the transaction into the $order variable
        try {
            $order = DB::transaction(function () use ($cart, $validatedData, $subtotal, $shipping, $total, $standardizedPhone, $coinsToRedeem, $finalTotal, $user) {

                // Re-verify stock for every cart item before committing
                $productIds = array_keys($cart);
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

                foreach ($cart as $id => $item) {
                    $product = $products->get($id);
                    if (! $product || $product->stock_quantity < $item['quantity']) {
                        throw new \Exception('stock_insufficient');
                    }
                }

                // Inside the transaction, we create the order
                $newOrder = Order::create([
                    'user_id' => auth()->id(),
                    'name' => $validatedData['name'] ?? (auth()->check() ? auth()->user()->name : 'Guest'),
                    'email' => $validatedData['email'],
                    'phone' => $standardizedPhone,
                    'address' => $validatedData['address'],
                    'division' => $validatedData['division'],
                    'district' => $validatedData['district'],
                    'postal_code' => $validatedData['postal_code'],
                    'delivery_method' => $validatedData['delivery'],
                    'payment_method' => $validatedData['payment'],
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shipping,
                    'total_amount' => $finalTotal,
                    'status' => 'pending',
                ]);

                $orderItems = collect($cart)->map(function ($item, $id) use ($newOrder) {
                    return [
                        'order_id'   => $newOrder->id,
                        'product_id' => $id,
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                })->values()->toArray();

                OrderItem::insert($orderItems);

                if ($coinsToRedeem > 0) {
                    $this->coinService->debit($user, $coinsToRedeem, "Coins redeemed on order #{$newOrder->id}");
                }

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

                // NOTICE THIS LINE: We must return the newly created order out of the transaction
                return $newOrder;
            });
        } catch (InsufficientCoinsException $e) {
            return back()->withErrors(['redeem_coins' => $e->getMessage()]);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'stock_insufficient') {
                return redirect()->route('home')
                    ->with('error', 'Sorry, an item in your cart just went out of stock. Please review your cart.');
            }
            throw $e;
        }

        // Now $order exists outside the transaction!
        session()->forget('cart');


        return redirect()->route('order.confirmation', $order->id)
                         ->with('success', 'Your order has been placed successfully!');
    }
}
