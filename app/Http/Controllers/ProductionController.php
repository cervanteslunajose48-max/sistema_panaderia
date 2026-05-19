<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Recipe;
use App\Models\Supply;
use App\Models\SupplyStock;
use App\Models\Warehouse;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    /**
     * Show form to register production.
     */
    /**
     * Show form to register production with Max Calculation.
     */
    public function create()
    {
        $products = Product::where('type', 'finished')->with(['variants.recipe.ingredients.supply.stocks'])->get();

        // Calculate Max Production for each variant
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                if ($variant->recipe) {
                    $maxPossible = 999999;
                    foreach ($variant->recipe->ingredients as $ingredient) {
                        // Total stock across all warehouses for this supply
                        $totalStock = $ingredient->supply->stocks->sum('quantity');

                        if ($ingredient->quantity > 0) {
                            $possible = floor($totalStock / $ingredient->quantity);
                            if ($possible < $maxPossible) {
                                $maxPossible = $possible;
                            }
                        }
                    }
                    $variant->max_production = $maxPossible == 999999 ? 0 : $maxPossible;
                } else {
                    $variant->max_production = 0; // No recipe = No production
                }
            }
        }

        // Recent History
        $recentProductions = InventoryMovement::where('type', 'production_in')
            ->with('productVariant.product')
            ->latest()
            ->take(5)
            ->get();

        return view('production.create', compact('products', 'recentProductions'));
    }

    /**
     * Get products list for AJAX search (API endpoint)
     */
    public function getProducts()
    {
        $products = Product::where('type', 'finished')
            ->where('status', 'active')
            ->with('variants.recipe.ingredients.supply.stocks')
            ->get();

        $productList = [];

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                // Calculate max production
                $maxProduction = 999999;
                if ($variant->recipe) {
                    foreach ($variant->recipe->ingredients as $ingredient) {
                        $totalStock = $ingredient->supply->stocks->sum('quantity');
                        if ($ingredient->quantity > 0) {
                            $possible = floor($totalStock / $ingredient->quantity);
                            if ($possible < $maxProduction) {
                                $maxProduction = $possible;
                            }
                        }
                    }
                } else {
                    $maxProduction = 0;
                }

                $productList[] = [
                    'id' => $variant->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant->name,
                    'current_stock' => $variant->current_stock ?? 0,
                    'max_production' => $maxProduction == 999999 ? 0 : $maxProduction,
                    'has_recipe' => $variant->recipe ? true : false
                ];
            }
        }

        return response()->json($productList);
    }

    /**
     * Store (Execute) production run.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product', 'recipe.ingredients.supply')->find($validated['product_variant_id']);

        if (!$variant->recipe) {
            return back()->with('error', 'Este producto no tiene una receta definida.');
        }

        try {
            DB::beginTransaction();

            // Verificar que existe al menos un warehouse
            $warehouse = Warehouse::first();
            if (!$warehouse) {
                throw new \Exception("No hay almacenes configurados. Por favor cree al menos un almacén desde Configuración.");
            }
            $warehouseId = $warehouse->id;
            $producedQty = $validated['quantity'];

            // 1. Deduct Ingredients & Log (mejorado para usar múltiples warehouses)
            foreach ($variant->recipe->ingredients as $ingredient) {
                $requiredQty = $ingredient->quantity * $producedQty;

                // Obtener todos los stocks disponibles de este insumo
                $stocks = SupplyStock::where('supply_id', $ingredient->supply_id)
                    ->where('quantity', '>', 0)
                    ->orderByDesc('quantity') // Deducir primero de los con más stock
                    ->get();

                $totalAvailable = $stocks->sum('quantity');

                if ($totalAvailable < $requiredQty) {
                    throw new \Exception(
                        "Stock insuficiente de {$ingredient->supply->name}. " .
                        "Disponible: " . number_format($totalAvailable, 2) . " {$ingredient->supply->base_unit}, " .
                        "Requerido: " . number_format($requiredQty, 2) . " {$ingredient->supply->base_unit}"
                    );
                }

                // Deducir de múltiples warehouses si es necesario
                $remaining = $requiredQty;
                foreach ($stocks as $stock) {
                    if ($remaining <= 0)
                        break;

                    $toDeduct = min($stock->quantity, $remaining);
                    $stock->decrement('quantity', $toDeduct);
                    $remaining -= $toDeduct;

                    // Log por cada warehouse usado
                    InventoryMovement::create([
                        'supply_id' => $ingredient->supply_id,
                        'warehouse_id' => $stock->warehouse_id,
                        'product_variant_id' => null,
                        'type' => 'production_out',
                        'quantity' => $toDeduct,
                        'description' => "Usado para producción: $producedQty x {$variant->product->name}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            // 2. Add Product Stock (SIEMPRE para productos terminados)
            // Asegurar que stock_track esté activado para productos terminados
            if (!$variant->stock_track) {
                $variant->update(['stock_track' => true]);
            }

            // SIEMPRE incrementar stock de productos terminados
            $variant->increment('current_stock', $producedQty);


            // 3. Log Production (products don't need warehouse_id, only supplies do)
            InventoryMovement::create([
                'product_variant_id' => $variant->id,
                'supply_id' => null, // This is a product, not a supply
                'warehouse_id' => null, // Products are tracked in variants, not warehouses
                'type' => 'production_in',
                'quantity' => $producedQty,
                'description' => "Producción Confirmada: $producedQty x {$variant->product->name}",
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('production.create')->with('success', "¡Producción Exitosa! Se agregaron {$producedQty} {$variant->product->name} al inventario.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Process batch production (multiple products at once).
     */
    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'batch_items' => 'required|json',
        ]);

        $batchItems = json_decode($validated['batch_items'], true);

        if (empty($batchItems)) {
            return back()->with('error', 'No hay productos en el lote.');
        }

        try {
            DB::beginTransaction();

            $successCount = 0;
            $totalValue = 0;
            $processedProducts = [];

            foreach ($batchItems as $item) {
                $variant = ProductVariant::with('product', 'recipe.ingredients.supply')->find($item['id']);

                if (!$variant || !$variant->recipe) {
                    throw new \Exception("Producto ID {$item['id']} no tiene receta definida.");
                }

                $producedQty = $item['quantity'];

                // Verificar warehouse
                $warehouse = Warehouse::first();
                if (!$warehouse) {
                    throw new \Exception("No hay almacenes configurados.");
                }

                // Deduct ingredients
                foreach ($variant->recipe->ingredients as $ingredient) {
                    $requiredQty = $ingredient->quantity * $producedQty;

                    $stocks = SupplyStock::where('supply_id', $ingredient->supply_id)
                        ->where('quantity', '>', 0)
                        ->orderByDesc('quantity')
                        ->get();

                    $totalAvailable = $stocks->sum('quantity');

                    if ($totalAvailable < $requiredQty) {
                        throw new \Exception(
                            "Stock insuficiente de {$ingredient->supply->name} para {$variant->product->name}. " .
                            "Disponible: {$totalAvailable}, Requerido: {$requiredQty}"
                        );
                    }

                    // Deducir stock
                    $remaining = $requiredQty;
                    foreach ($stocks as $stock) {
                        if ($remaining <= 0)
                            break;

                        $toDeduct = min($stock->quantity, $remaining);
                        $stock->decrement('quantity', $toDeduct);
                        $remaining -= $toDeduct;

                        InventoryMovement::create([
                            'supply_id' => $ingredient->supply_id,
                            'warehouse_id' => $stock->warehouse_id,
                            'type' => 'production_out',
                            'quantity' => $toDeduct,
                            'description' => "Lote: $producedQty x {$variant->product->name}",
                            'user_id' => auth()->id(),
                        ]);
                    }
                }

                // Activar stock tracking si no está
                if (!$variant->stock_track) {
                    $variant->update(['stock_track' => true]);
                }

                // Incrementar stock del producto
                $variant->increment('current_stock', $producedQty);

                // Log production
                InventoryMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'production_in',
                    'quantity' => $producedQty,
                    'description' => "Producción en Lote",
                    'user_id' => auth()->id(),
                ]);

                $successCount++;
                $totalValue += $producedQty * $variant->price;
                $processedProducts[] = "{$producedQty} x {$variant->product->name}";
            }

            DB::commit();

            $productsList = implode(', ', $processedProducts);
            $currencySymbol = \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$';
            $message = "¡Lote Procesado Exitosamente! Productos: {$productsList}. Valor total: {$currencySymbol} " . number_format($totalValue, 2);

            return redirect()->route('production.create')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar lote: ' . $e->getMessage());
        }
    }
}
