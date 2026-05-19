<?php

namespace App\Http\Controllers;

use App\Models\SpecialOrder;
use App\Models\SpecialOrderItem;
use App\Models\Customer;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecialOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SpecialOrder::with('customer')->latest('pickup_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        return view('special_orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::where('status', true)->get();
        // Get all variants with product name
        $products = ProductVariant::with('product')->whereHas('product', function ($q) {
            $q->where('status', 'active');
        })->get()->map(function ($variant) {
            $variant->full_name = $variant->product->name . ' - ' . $variant->name;
            return $variant;
        });

        return view('special_orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'pickup_date' => 'required|date|after:now',
            'deposit_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $order = SpecialOrder::create([
                'customer_id' => $request->customer_id,
                'pickup_date' => $request->pickup_date,
                'status' => 'pending',
                'deposit_amount' => $request->deposit_amount ?? 0,
                'notes' => $request->notes,
                'total_amount' => 0, // Will update below
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;

                SpecialOrderItem::create([
                    'special_order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'specifications' => $item['specifications'] ?? null,
                ]);
            }

            $order->update(['total_amount' => $total]);

            DB::commit();

            return redirect()->route('special-orders.index')->with('success', 'Pedido especial registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar pedido: ' . $e->getMessage())->withInput();
        }
    }

    public function show(SpecialOrder $specialOrder)
    {
        $specialOrder->load(['customer', 'items.productVariant.product']);
        return view('special_orders.show', compact('specialOrder'));
    }

    public function updateStatus(Request $request, SpecialOrder $specialOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,in_production,ready,delivered,cancelled',
        ]);

        $specialOrder->update(['status' => $request->status]);

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
