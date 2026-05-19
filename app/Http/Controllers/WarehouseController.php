<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\SupplyStock;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses.
     */
    public function index(Request $request)
    {
        $query = Warehouse::query();

        // Search functionality (case-insensitive)
        if ($request->has('search') && $request->search) {
            $searchTerm = strtolower($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(location) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        $warehouses = $query->paginate(12);

        // Get stock count for each warehouse
        foreach ($warehouses as $warehouse) {
            $warehouse->stock_count = SupplyStock::where('warehouse_id', $warehouse->id)
                ->where('quantity', '>', 0)
                ->count();
        }

        return view('warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        return view('warehouses.create');
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
            'location' => 'nullable|string|max:500',
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')
            ->with('success', '¡Almacén creado exitosamente!');
    }

    /**
     * Display the specified warehouse with its supplies.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['stocks.supply.supplier']);

        // Get stocks grouped by supply with additional info
        $stocks = $warehouse->stocks()
            ->with('supply.supplier')
            ->where('quantity', '>', 0)
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'supply' => $stock->supply,
                    'quantity' => $stock->quantity,
                    'total_value' => $stock->quantity * $stock->supply->cost,
                ];
            });

        $totalValue = $stocks->sum('total_value');

        return view('warehouses.show', compact('warehouse', 'stocks', 'totalValue'));
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name,' . $warehouse->id,
            'location' => 'nullable|string|max:500',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')
            ->with('success', '¡Almacén actualizado exitosamente!');
    }

    /**
     * Toggle warehouse status (activate/deactivate).
     */
    public function toggleStatus(Warehouse $warehouse)
    {
        $warehouse->status = !$warehouse->status;
        $warehouse->save();

        $status = $warehouse->status ? 'activado' : 'desactivado';
        return back()->with('success', "Almacén {$status} correctamente.");
    }
}
