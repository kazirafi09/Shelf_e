<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoinLedger;
use App\Services\CoinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function __construct(private readonly CoinService $coinService) {}

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
        Gate::authorize('update', $order);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $previousStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Award coins when order is delivered — only once per order
        if ($request->status === 'delivered' && $previousStatus !== 'delivered' && $order->user_id) {
            $alreadyCredited = CoinLedger::where('user_id', $order->user_id)
                ->where('description', 'LIKE', "%order #{$order->id}%")
                ->where('type', 'credit')
                ->exists();

            if (! $alreadyCredited) {
                $coinsEarned = (int) floor($order->subtotal / 10);
                if ($coinsEarned > 0) {
                    $order->load('user');
                    $this->coinService->credit(
                        $order->user,
                        $coinsEarned,
                        "Earned from order #{$order->id} (৳{$order->subtotal} subtotal)"
                    );
                }
            }
        }

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
