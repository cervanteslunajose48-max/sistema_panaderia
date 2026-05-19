<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with key statistics.
     */
    public function index(): View
    {
        // 1. Daily Sales
        $today = Carbon::today();
        $dailySales = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total');

        // 2. Pending Orders
        $pendingOrders = Order::where('status', 'pending')->count();

        // 3. Low Stock Products (assuming 'quantity' column exists or similar logic)
        // Adjust column name based on your actual migration for products/inventory
        $lowStockCount = Product::where('status', 'active')
            // ->where('stock', '<', 10) // Uncomment when connecting real stock
            ->count();

        // 4. Total Customers
        $totalCustomers = Customer::count();

        // 5. Recent Orders
        $recentOrders = Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // 6. Chart Data: Sales Last 7 Days
        $salesLabels = [];
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $salesLabels[] = $date->format('j M'); // e.g. "12 Feb"
            $salesData[] = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total');
        }

        // 7. Chart Data: Top Selling Categories
        $topCategories = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', \Illuminate\Support\Facades\DB::raw('sum(order_items.subtotal) as total_sales'))
            ->groupBy('categories.name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        $categoryLabels = $topCategories->pluck('name');
        $categoryData = $topCategories->pluck('total_sales');

        // 8. Chart Data: Sales by Day of Week (Last 30 days)
        // Database agnostic approach: Fetch data and process in PHP
        $ordersLast30Days = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->get();

        $weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weekSalesData = array_fill(0, 7, 0);

        foreach ($ordersLast30Days as $order) {
            $dayName = $order->created_at->format('l'); // e.g. "Monday"
            $key = array_search($dayName, $weekDays);
            if ($key !== false) {
                $weekSalesData[$key] += $order->total;
            }
        }

        // Translate days for display if needed, but keeping English keys for logic
        // The view receives $weekDays which are English, we can map them in the view or here if we want translated labels.
        // For the chart labels in the view, we manually set 'Lun', 'Mar', etc., so sending just data is fine.
        // We just need to ensure $weekSalesData matches that order (Mon -> Sun).
        // My array_search above does exactly that.

        // 9. Chart Data: Top 5 Specific Products
        $topProducts = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(\Illuminate\Support\Facades\DB::raw("CONCAT(products.name, ' - ', product_variants.name) as full_name"), \Illuminate\Support\Facades\DB::raw('sum(order_items.quantity) as total_qty'))
            ->groupBy('full_name') // Group by full name to aggregate variants
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $topProductLabels = $topProducts->pluck('full_name');
        $topProductData = $topProducts->pluck('total_qty');

        return view('dashboard', compact(
            'dailySales',
            'pendingOrders',
            'lowStockCount',
            'totalCustomers',
            'recentOrders',
            'salesLabels',
            'salesData',
            'categoryLabels',
            'categoryData',
            'weekDays',
            'weekSalesData',
            'topProductLabels',
            'topProductData'
        ));
    }
}
