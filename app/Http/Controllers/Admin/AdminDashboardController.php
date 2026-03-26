<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Calculate Stats for the "Stats Cards"
        $totalSales = Order::where('status', 'completed')->sum('total_amount');
        $totalOrders = Order::count();
        $totalBooks = Product::count();
        $totalCustomers = User::count();

        // 2. Get the latest 10 orders to show in the table
        $recentOrders = Order::orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact(
            'totalSales', 
            'totalOrders', 
            'totalBooks', 
            'totalCustomers', 
            'recentOrders'
        ));
    }
}