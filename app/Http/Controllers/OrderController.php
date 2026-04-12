<?php

namespace App\Http\Controllers;

use App\Models\CoinShippingReward;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\UserAddress;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        $cartItems = session()->get('cart', []);
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $insideDhakaRate  = (int) Setting::get('shipping_inside_dhaka', 60);
        $outsideDhakaRate = (int) Setting::get('shipping_outside_dhaka', 150);
        // Default preview uses inside-Dhaka rate (Dhaka is pre-selected).
        // Free shipping for Dhaka orders ≥ ৳1500.
        $shipping = count($cartItems) > 0
            ? ($subtotal >= 1500 ? 0 : $insideDhakaRate)
            : 0;
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

        $bkashNumber = Setting::get('bkash_number', '');

        // Best unused shipping reward for this user (highest discount first)
        $activeReward = null;
        if (auth()->check()) {
            $activeReward = CoinShippingReward::where('user_id', auth()->id())
                ->where('used', false)
                ->orderByDesc('shipping_discount')
                ->first();
        }

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total', 'lastOrder', 'savedAddresses', 'insideDhakaRate', 'outsideDhakaRate', 'bkashNumber', 'activeReward'));
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

        // Allow access if:
        //   (a) a logged-in user owns this order, OR
        //   (b) a guest just placed this order (ID stored in session after store())
        $ownedByUser  = auth()->check() && $order->user_id === auth()->id();
        $ownedByGuest = (int) session('confirmation_order_id') === (int) $id;

        abort_unless($ownedByUser || $ownedByGuest, 403, 'You are not authorized to view this order.');

        // Remove the one-time session key so the URL cannot be shared to bypass auth
        session()->forget('confirmation_order_id');

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
            'payment'      => 'required|in:cod,bkash',
            'bkash_transaction_id' => 'required_if:payment,bkash|nullable|string|max:100',
            'coupon_code'  => 'nullable|string|max:50',
            'coin_reward_id' => 'nullable|integer|exists:coin_shipping_rewards,id',
        ]);

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $insideDhakaRate  = (int) Setting::get('shipping_inside_dhaka', 60);
        $outsideDhakaRate = (int) Setting::get('shipping_outside_dhaka', 150);
        $deliveryMethod   = $validatedData['division'] === 'Dhaka' ? 'inside_dhaka' : 'outside_dhaka';

        // Free shipping for Dhaka orders ≥ ৳1500
        if ($deliveryMethod === 'inside_dhaka' && $subtotal >= 1500) {
            $shipping = 0;
        } else {
            $shipping = $deliveryMethod === 'inside_dhaka' ? $insideDhakaRate : $outsideDhakaRate;
        }

        $total = $subtotal + $shipping;

        // ── Voucher validation ────────────────────────────────────────────────
        // Resolved outside the transaction so validation errors return early
        // before any DB writes occur.
        $couponCode      = strtoupper(trim($request->input('coupon_code', '')));
        $discountAmount  = 0;
        $appliedVoucher  = null;
        $subscriberForCoupon = null;

        if ($couponCode !== '') {
            if (! auth()->check()) {
                return back()
                    ->withErrors(['coupon_code' => 'You must be logged in to apply a discount code.'])
                    ->withInput();
            }

            $voucher = Voucher::where('code', $couponCode)->first();

            if (! $voucher || ! $voucher->isUsable()) {
                return back()
                    ->withErrors(['coupon_code' => 'This discount code is invalid or has expired.'])
                    ->withInput();
            }

            if ($subtotal < $voucher->min_order_amount) {
                return back()
                    ->withErrors(['coupon_code' => "This code requires a minimum order of ৳" . number_format($voucher->min_order_amount, 0) . "."])
                    ->withInput();
            }

            // Check per-user usage limit via voucher_usages table
            if ($voucher->hasBeenUsedByUser(auth()->id())) {
                return back()
                    ->withErrors(['coupon_code' => 'You have already used this discount code.'])
                    ->withInput();
            }

            // Legacy FIRST15 backward-compat: also check subscribers.discount_used
            // for users who applied FIRST15 before the new voucher system existed.
            if ($voucher->code === 'FIRST15') {
                $subscriberForCoupon = Subscriber::where('email', auth()->user()->email)->first();
                if (! $subscriberForCoupon) {
                    return back()
                        ->withErrors(['coupon_code' => 'FIRST15 is only available to newsletter subscribers.'])
                        ->withInput();
                }
                if ($subscriberForCoupon->discount_used) {
                    return back()
                        ->withErrors(['coupon_code' => 'You have already used this discount code.'])
                        ->withInput();
                }
            }

            $discountAmount = $voucher->calculateDiscount($subtotal);
            $total         -= $discountAmount;
            $appliedVoucher = $voucher;
        }
        // ─────────────────────────────────────────────────────────────────────

        $user = auth()->user();

        // ── Coin shipping reward ──────────────────────────────────────────────
        $appliedReward          = null;
        $rewardShippingDiscount = 0;

        if ($user && $request->filled('coin_reward_id')) {
            $reward = CoinShippingReward::where('id', $validatedData['coin_reward_id'])
                ->where('user_id', $user->id)
                ->where('used', false)
                ->first();

            if ($reward) {
                $rewardShippingDiscount = min($reward->shipping_discount, $shipping);
                $shipping              -= $rewardShippingDiscount;
                $total                  = $subtotal + $shipping - $discountAmount;
                $appliedReward          = $reward;
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        $cleanPhone = preg_replace('/[^0-9]/', '', $validatedData['phone']);
        $standardizedPhone = '+880' . ltrim($cleanPhone, '0');

        // NOTICE THIS LINE: We are capturing the output of the transaction into the $order variable
        try {
            $order = DB::transaction(function () use ($cart, $validatedData, $subtotal, $shipping, $total, $standardizedPhone, $user, $discountAmount, $couponCode, $subscriberForCoupon, $appliedVoucher, $deliveryMethod, $appliedReward) {

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
                    'user_id'         => auth()->id(),
                    'name'            => $validatedData['name'] ?? (auth()->check() ? auth()->user()->name : 'Guest'),
                    'email'           => $validatedData['email'],
                    'phone'           => $standardizedPhone,
                    'address'         => $validatedData['address'],
                    'division'        => $validatedData['division'],
                    'district'        => $validatedData['district'],
                    'postal_code'     => $validatedData['postal_code'] ?? null,
                    'delivery_method' => $deliveryMethod,
                    'payment_method'  => $validatedData['payment'],
                    'bkash_transaction_id' => $validatedData['payment'] === 'bkash' ? ($validatedData['bkash_transaction_id'] ?? null) : null,
                    'subtotal'        => $subtotal,
                    'shipping_cost'   => $shipping,
                    'discount_amount' => $discountAmount,
                    'coupon_code'     => $couponCode ?: null,
                    'coin_reward_id'  => $appliedReward?->id,
                    'total_amount'    => $total,
                    'status'          => 'pending',
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

                // ── Record voucher usage ──────────────────────────────────────
                // Inside the transaction so it rolls back on failure.
                if ($appliedVoucher) {
                    VoucherUsage::create([
                        'voucher_id' => $appliedVoucher->id,
                        'user_id'    => $user->id,
                        'order_id'   => $newOrder->id,
                        'used_at'    => now(),
                    ]);
                    $appliedVoucher->increment('used_count');

                    // Legacy FIRST15: also mark the subscriber record as used
                    if ($subscriberForCoupon) {
                        $subscriberForCoupon->update(['discount_used' => true]);
                    }
                }
                // ─────────────────────────────────────────────────────────────

                // ── Mark shipping reward as used ──────────────────────────────
                if ($appliedReward) {
                    $appliedReward->update([
                        'used'     => true,
                        'order_id' => $newOrder->id,
                    ]);
                }
                // ─────────────────────────────────────────────────────────────

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
        } catch (\Exception $e) {
            if ($e->getMessage() === 'stock_insufficient') {
                return redirect()->route('home')
                    ->with('error', 'Sorry, an item in your cart just went out of stock. Please review your cart.');
            }
            throw $e;
        }

        // Now $order exists outside the transaction!
        session()->forget('cart');
        session()->put('confirmation_order_id', $order->id);

        // Send order confirmation email (queued so it doesn't delay the redirect)
        Mail::to($order->email)->queue(new OrderConfirmation($order->load('items.product')));

        return redirect()->route('order.confirmation', $order->id)
                         ->with('success', 'Your order has been placed successfully!');
    }
}
