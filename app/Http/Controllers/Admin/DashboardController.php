<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('status', 'confirmed')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
        ];

        $recent_products = Product::with('category')->latest()->take(5)->get();
        $low_stock_products = Product::where('stock_quantity', '<', 10)->with('category')->get();
        $recent_orders = Order::with(['customer', 'orderItems'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_products', 'low_stock_products', 'recent_orders'));
    }
}
