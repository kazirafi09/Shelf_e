<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // Make sure to import your Order model!

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Fetch the 5 most recent orders for THIS specific user
        $recentOrders = Order::where('user_id', $user->id)
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        // Get a total count of their orders
        $totalOrders = Order::where('user_id', $user->id)->count();

        return view('dashboard', compact('user', 'recentOrders', 'totalOrders'));
    }
}