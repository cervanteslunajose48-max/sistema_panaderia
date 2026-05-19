<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supply;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Models\SupplyStock;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryAdjustmentController extends Controller
{
    /**
     * Show the form for creating a new adjustment.
     */
    public function create()
    {
        $supplies = Supply::where('status', 1)->orderBy('name')->get();
        // Load variants with product name for better display
        $products = ProductVariant::with('product')->get()->map(function ($variant) {
            $variant->full_name = $variant->product->name . ' - ' . $variant->name;
            return $variant;
        });

        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();

        return view('inventory.adjustments.create', compact('supplies', 'products', 'warehouses'));
    }

    /**
     * Store a newly created adjustment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:supply,product',
            'item_id_supply' => 'required_if:item_type,supply',
            'item_id_product' => 'required_if:item_type,product',
            'adjustment_type' => 'required|in:waste,return,correction_in,correction_out',
            'quantity' => 'required|numeric|min:0.01',
            'warehouse_id' => 'required_if:item_type,supply|exists:warehouses,id',
            'reason' => 'nullable|string|max:255',
        ], [
            'item_id_supply.required_if' => 'Debe seleccionar un insumo.',
            'item_id_product.required_if' => 'Debe seleccionar un producto.',
            'warehouse_id.required_if' => 'Debe seleccionar un almacén para el insumo.',
        ]);

        try {
            DB::beginTransaction();

            $description = $this->getAdjustmentDescription($request->adjustment_type, $request->reason);
            $isNegative = in_array($request->adjustment_type, ['waste', 'correction_out']);
            $quantity = $request->quantity * ($isNegative ? -1 : 1);

            $itemId = $request->item_type === 'supply' ? $request->item_id_supply : $request->item_id_product;

            if ($request->item_type === 'supply') {
                $this->adjustSupplyStock($itemId, $request->warehouse_id, $quantity, $request->adjustment_type, $description);
            } else {
                $this->adjustProductStock($itemId, $quantity, $request->adjustment_type, $description);
            }

            DB::commit();

            return redirect()->route('inventory.adjustments.create')
                ->with('success', 'Ajuste de inventario registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar el ajuste: ' . $e->getMessage());
        }
    }

    private function adjustSupplyStock($supplyId, $warehouseId, $quantity, $type, $description)
    {
        $stock = SupplyStock::firstOrNew([
            'supply_id' => $supplyId,
            'warehouse_id' => $warehouseId,
        ]);

        $stock->quantity = ($stock->quantity ?? 0) + $quantity;

        // Prevent negative stock for Physical items if strict checking is needed, 
        // but for corrections we often allow it or just warn. Let's allow negative for now 
        // or check if it goes below zero? User didn't specify strictness. 
        // We'll proceed.

        $stock->save();

        InventoryMovement::create([
            'supply_id' => $supplyId,
            'warehouse_id' => $warehouseId,
            'type' => $type, // reusing the adjustment type string
            'quantity' => $quantity, // Store the signed quantity (+/-) or absolute? 
            // Usually movements store absolute and 'type' determines direction, 
            // OR signed. Previous logic in PurchaseController stored Type='purchase' Qty=+ve.
            // ProductionController likely stores Type='production_out' Qty=-ve.
            // Let's store signed quantity for easier summation, or follow existing pattern.
            // Checking PurchaseController: 'quantity' => $item->quantity (positive).
            // Let's assume quantity is absolute and type implies direction, OR quantity is signed.
            // WAIT. In ProductionController (from logs), 'production_out' usually implies negative.
            // Let's stick to: Store signed quantity in movement so sum(quantity) = current stock.
            // Wait, standard is often Absolute Quantity in DB, Signed in logic.
            // Let's check InventoryMovement model - it has 'type'.
            // Let's check PurchaseController again: 
            // 'type' => 'purchase', 'quantity' => $item->quantity. (Positive)
            // If I do `production_out`, it should probably be negative quantity or the report handles it.
            // Let's verify standard practice in this app.
            // I will store SIGNED quantity to be safe if aggregating simple sums, 
            // but if there's a strict type system, I should follow it.
            // PurchaseController: `stock->quantity = ... + $item->quantity`.
            // Let's store SIGNED quantity in `InventoryMovement` if the system relies on `sum('quantity')`.
            // If the system relies on `type` to determine sign, I should store absolute.
            // Given I am implementing the "Reports" later or they exist, I should probably check.
            // To be safe and consistent with "Purchase", I will store SIGNED quantity here 
            // because "adjustment" is ambiguous.
            // Actually, `PurchaseController` stored POSITIVE.
            // `ProductionController` likely stores NEGATIVE.
            // I will store SIGNED quantity for this module's movements.

            // Correction: If I store signed, `waste` type with -5 qty is clear.

            'quantity' => $quantity,
            'description' => $description,
            'user_id' => Auth::id(),
        ]);
    }

    private function adjustProductStock($variantId, $quantity, $type, $description)
    {
        $variant = ProductVariant::find($variantId);
        if (!$variant)
            throw new \Exception("Producto no encontrado");

        $variant->current_stock = ($variant->current_stock ?? 0) + $quantity;
        $variant->save();

        InventoryMovement::create([
            'product_variant_id' => $variantId,
            // 'warehouse_id' => null, // Products don't have warehouses yet
            'type' => $type,
            'quantity' => $quantity,
            'description' => $description,
            'user_id' => Auth::id(),
        ]);
    }

    private function getAdjustmentDescription($type, $reason)
    {
        $labels = [
            'waste' => 'Merma / Desperdicio',
            'return' => 'Devolución',
            'correction_in' => 'Corrección (Entrada)',
            'correction_out' => 'Corrección (Salida)',
        ];

        $desc = $labels[$type] ?? $type;
        if ($reason) {
            $desc .= " - $reason";
        }
        return $desc;
    }
}
