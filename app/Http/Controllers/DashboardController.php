<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; 

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // FIX A-4: Explicit authorization fail-safe
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Fetch the 5 most recent orders for THIS specific user
        // Added with('items.product') to prevent N+1 queries on the dashboard!
        $recentOrders = Order::where('user_id', $user->id)
                             ->with('items.product') 
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        // Get a total count of their orders
        $totalOrders = Order::where('user_id', $user->id)->count();

        return view('dashboard', compact('user', 'recentOrders', 'totalOrders'));
    }
}