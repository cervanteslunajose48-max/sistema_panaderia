<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\Supplier;
use App\Models\SupplyStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supply::with(['supplier', 'stocks']);

        // Filter by search term (case-insensitive)
        if ($request->filled('search')) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->search) . '%']);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'critical') {
                // Get supplies with stock below or equal to min_stock
                $query->whereHas('stocks', function ($q) {
                    $q->havingRaw('SUM(quantity) <= supplies.min_stock');
                });
            } elseif ($request->stock_status === 'sufficient') {
                // Get supplies with stock above min_stock
                $query->whereHas('stocks', function ($q) {
                    $q->havingRaw('SUM(quantity) > supplies.min_stock');
                });
            }
        }

        $supplies = $query->latest()->paginate(10)->withQueryString();
        $suppliers = \App\Models\Supplier::all();
        return view('supplies.index', compact('supplies', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_unit' => 'required|string|in:kg,g,l,ml,unit',
            'cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'initial_stock' => 'nullable|numeric|min:0',
        ]);

        $supply = Supply::create([
            'name' => $validated['name'],
            'base_unit' => $validated['base_unit'],
            'cost' => $validated['cost'],
            'supplier_id' => $validated['supplier_id'] ?? null,
        ]);

        // If warehouse_id and initial_stock are provided, create stock record
        if ($request->filled('warehouse_id') && $request->filled('initial_stock') && $request->initial_stock > 0) {
            SupplyStock::create([
                'supply_id' => $supply->id,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->initial_stock,
            ]);
        }

        return redirect()->route('supplies.index')->with('success', 'Insumo creado correctamente.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supply $supply)
    {
        $supply->load(['stocks.warehouse', 'supplier']);
        $suppliers = Supplier::all();
        return view('supplies.edit', compact('supply', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supply $supply)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_stocks' => 'nullable|array',
            'warehouse_stocks.*' => 'nullable|numeric|min:0',
        ]);

        $supply->update([
            'name' => $validated['name'],
            'cost' => $validated['cost'],
            'min_stock' => $validated['min_stock'] ?? 0,
            'supplier_id' => $validated['supplier_id'] ?? null,
        ]);

        // Sync warehouse stocks
        if ($request->has('warehouse_stocks')) {
            foreach ($request->warehouse_stocks as $warehouseId => $quantity) {
                $quantity = floatval($quantity);

                // Find existing stock record
                $stock = SupplyStock::where('supply_id', $supply->id)
                    ->where('warehouse_id', $warehouseId)
                    ->first();

                if ($quantity > 0) {
                    // Create or update stock
                    if ($stock) {
                        $stock->update(['quantity' => $quantity]);
                    } else {
                        SupplyStock::create([
                            'supply_id' => $supply->id,
                            'warehouse_id' => $warehouseId,
                            'quantity' => $quantity,
                        ]);
                    }
                } elseif ($stock && $quantity == 0) {
                    // Delete stock if quantity is 0
                    $stock->delete();
                }
            }
        }

        return redirect()->route('supplies.index')->with('success', 'Insumo actualizado correctamente.');
    }

    public function show(Supply $supply)
    {
        $supply->load(['stocks.warehouse', 'supplier']);

        $movements = \App\Models\InventoryMovement::where('supply_id', $supply->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('supplies.show', compact('supply', 'movements'));
    }

    public function destroy(Supply $supply)
    {
        // $supply->delete();
        return back()->with('error', 'La eliminación está deshabilitada. Use la opción Activar/Desactivar.');
    }

    public function toggleStatus(Supply $supply)
    {
        $supply->status = !$supply->status;
        $supply->save();
        $status = $supply->status ? 'activado' : 'desactivado';
        return back()->with('success', "Insumo $status correctamente.");
    }

    /**
     * Add stock to a supply (Manual Adjustment / Purchase).
     */
    public function restock(Request $request, Supply $supply)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'cost' => 'nullable|numeric', // Update average cost if provided
        ]);

        // Updates or Create stock entry in Default Warehouse (ID 1)
        // In a real app we would select Warehouse
        $warehouseId = 1;

        // Check if Default Warehouse exists, if not create it (safe guard)
        $warehouse = Warehouse::firstOrCreate(
            ['id' => 1],
            ['name' => 'Almacén Principal']
        );

        // Simple approach: One stock record per supply per warehouse (No batches for now to simplify)
        $stock = SupplyStock::firstOrNew([
            'supply_id' => $supply->id,
            'warehouse_id' => $warehouse->id,
        ]);

        // Initialize quantity to 0 if it's a new record
        if (!$stock->exists) {
            $stock->quantity = 0;
        }

        $stock->quantity += $validated['quantity'];
        $stock->save();

        // Only update cost if a valid value is provided (not empty string)
        if ($request->filled('cost') && $request->cost > 0) {
            $supply->update(['cost' => $request->cost]);
        }

        return back()->with('success', 'Stock actualizado (+ ' . $validated['quantity'] . ' ' . $supply->base_unit . ')');
    }

    /**
     * Search supplies for customization (Ajax)
     */
    public function search(Request $request)
    {
        $term = $request->get('q');

        $supplies = Supply::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($term) . '%'])
            ->select('id', 'name', 'base_unit', 'cost')
            ->limit(20)
            ->get();

        return response()->json($supplies);
    }
}
