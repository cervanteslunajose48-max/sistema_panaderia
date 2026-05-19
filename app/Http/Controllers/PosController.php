<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        // Phase 12: Cash Control Check
        $activeRegister = \App\Models\CashRegister::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$activeRegister) {
            return redirect()->route('cash-registers.create')->with('warning', 'Debe abrir caja antes de vender.');
        }

        $categories = Category::all();
        // Eager load variants, category, and primary image
        $products = Product::with(['variants', 'category', 'primaryImage'])->where('status', 'active')->get();
        $customers = Customer::all();

        return view('pos.index', compact('categories', 'products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $itemsToCreate = [];

            // 1. Calculate Total and Validate Stock
            foreach ($validated['items'] as $item) {
                $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);

                if ($variant->stock_track && $variant->current_stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para {$variant->product->name} - {$variant->name}");
                }

                $subtotal = $variant->price * $item['quantity'];
                $total += $subtotal;

                $itemsToCreate[] = [
                    'variant' => $variant,
                    'quantity' => $item['quantity'],
                    'price' => $variant->price,
                    'subtotal' => $subtotal,
                ];
            }

            // 2. Create Order
            $order = Order::create([
                'user_id' => Auth::id(), // Cashier
                'customer_id' => $request->customer_id,
                'status' => 'completed', // Direct sale
                'type' => 'pos',
                'total' => $total,
            ]);

            // 3. Create Items and Update Stock
            foreach ($itemsToCreate as $data) {
                $order->items()->create([
                    'product_variant_id' => $data['variant']->id,
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                    'subtotal' => $data['subtotal'],
                ]);

                if ($data['variant']->stock_track) {
                    $data['variant']->decrement('current_stock', $data['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'order_id' => $order->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
