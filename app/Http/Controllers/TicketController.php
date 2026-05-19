<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function show(Order $order)
    {
        $order->load('items.variant.product', 'user', 'customer');
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        return view('sales.ticket', compact('order', 'settings'));
    }
}
