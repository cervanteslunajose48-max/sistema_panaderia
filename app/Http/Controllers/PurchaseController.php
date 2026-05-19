<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Supply;
use App\Models\SupplyStock;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'warehouse', 'user'])
            ->latest('purchase_date')
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $supplies = Supply::where('status', 1)->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'warehouses', 'supplies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|exists:supplies,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'warehouse_id' => $request->warehouse_id,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'status' => 'pending', // Default to pending, must appear as "Receive" button in show
                'notes' => $request->notes,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'supply_id' => $item['supply_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                ]);

                // Handle Immediate Reception
                if ($request->has('receive_immediately') && $request->receive_immediately) {
                    // Update Stock
                    $stock = SupplyStock::firstOrNew([
                        'supply_id' => $item['supply_id'],
                        'warehouse_id' => $request->warehouse_id,
                    ]);
                    $stock->quantity = ($stock->quantity ?? 0) + $item['quantity'];
                    $stock->save();

                    // Update Supply Link Cost (Last Price user bought at)
                    Supply::where('id', $item['supply_id'])->update(['cost' => $item['unit_cost']]);

                    // Create Movement
                    InventoryMovement::create([
                        'supply_id' => $item['supply_id'],
                        'warehouse_id' => $request->warehouse_id,
                        'type' => 'purchase',
                        'quantity' => $item['quantity'],
                        'description' => "Compra #{$purchase->id} - {$purchase->supplier->name} (Ingreso Inmediato)",
                        'user_id' => Auth::id(),
                        'created_at' => $request->purchase_date . ' ' . date('H:i:s'), // Use purchase date
                    ]);
                }
            }

            if ($request->has('receive_immediately') && $request->receive_immediately) {
                $purchase->update(['status' => 'received']);
            }

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Compra registrada correctamente. Pendiente de recepción.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['items.supply', 'supplier', 'warehouse', 'user']);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented for now/simple inventory
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not implemented
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'Solo se pueden eliminar compras pendientes.');
        }

        $purchase->items()->delete();
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Compra eliminada.');
    }

    public function receive(Purchase $purchase)
    {
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'Esta compra ya ha sido procesada.');
        }

        try {
            DB::beginTransaction();

            // Update Stock
            foreach ($purchase->items as $item) {
                // Find or create stock record for this warehouse
                $stock = SupplyStock::firstOrNew([
                    'supply_id' => $item->supply_id,
                    'warehouse_id' => $purchase->warehouse_id,
                ]);

                $stock->quantity = ($stock->quantity ?? 0) + $item->quantity;
                $stock->save();

                // Update Supply Cost (Average or Last Price - let's do Last Price for now)
                $item->supply->update(['cost' => $item->unit_cost]);

                // Create Inventory Movement
                InventoryMovement::create([
                    'supply_id' => $item->supply_id,
                    'warehouse_id' => $purchase->warehouse_id,
                    'type' => 'purchase',
                    'quantity' => $item->quantity,
                    'description' => "Compra #{$purchase->id} - {$purchase->supplier->name}",
                    'user_id' => Auth::id(),
                ]);
            }

            $purchase->update(['status' => 'received']);

            DB::commit();

            return back()->with('success', 'Compra recibida. Inventario actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al recibir la compra: ' . $e->getMessage());
        }
    }
}
