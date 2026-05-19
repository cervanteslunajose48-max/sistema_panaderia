<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display inventory movements history with filters.
     */
    /**
     * Display inventory movements history with filters.
     */
    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);

        $movements = $query->paginate(20)->withQueryString();
        $types = $this->getTypes();

        return view('inventory.index', compact('movements', 'types'));
    }

    public function exportCsv(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $movements = $query->get();

        $filename = "historial_inventario_" . date('Y-m-d_H-i') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Fecha', 'Tipo', 'Producto/Insumo', 'Almacén', 'Cantidad', 'Usuario', 'Descripción'];

        $callback = function () use ($movements, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // Add BOM for Excel if needed, but standard CSV usually fine

            foreach ($movements as $movement) {
                // Determine item name
                $itemName = '-';
                if ($movement->productVariant) {
                    $itemName = $movement->productVariant->product->name . ' - ' . $movement->productVariant->name;
                } elseif ($movement->supply) {
                    $itemName = $movement->supply->name;
                }

                fputcsv($file, [
                    $movement->created_at->format('d/m/Y H:i'),
                    $movement->type,
                    $itemName,
                    $movement->warehouse ? $movement->warehouse->name : '-',
                    $movement->quantity,
                    $movement->user ? $movement->user->name : '-',
                    $movement->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function print(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $movements = $query->get();
        $types = $this->getTypes();

        return view('inventory.print', compact('movements', 'types'));
    }

    private function getFilteredQuery(Request $request)
    {
        $query = InventoryMovement::with([
            'supply',
            'productVariant.product',
            'warehouse',
            'user'
        ])->latest();

        // Filter by type
        if ($request->filled('type') && $request->type !== 'Todos') {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        // Filter by item type (supply or product)
        if ($request->filled('item_type')) {
            if ($request->item_type === 'supply') {
                $query->whereNotNull('supply_id');
            } elseif ($request->item_type === 'product') {
                $query->whereNotNull('product_variant_id');
            }
        }

        return $query;
    }

    private function getTypes()
    {
        return InventoryMovement::select('type')
            ->distinct()
            ->pluck('type')
            ->toArray();
    }
}
