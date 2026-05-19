<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Order::with(['customer', 'items.variant.product', 'user']);

        // Filter by dates
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        
        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('orders.index', compact('orders', 'customers'));
    }

    public function show(\App\Models\Order $order)
    {
        $order->load(['customer', 'items.variant.product', 'user', 'payments']);
        return view('orders.show', compact('order'));
    }
}
