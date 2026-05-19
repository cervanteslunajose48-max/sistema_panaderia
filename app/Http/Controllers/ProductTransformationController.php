<?php

namespace App\Http\Controllers;

use App\Models\ProductTransformation;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductTransformationController extends Controller
{
    public function create()
    {
        $products = ProductVariant::with('product')->get()->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->product->name . ' - ' . $variant->name . ' (Stock: ' . $variant->current_stock . ')',
                'stock' => $variant->current_stock
            ];
        });

        return view('inventory.transformations.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_variant_id' => 'required|exists:product_variants,id',
            'target_variant_id' => 'required|exists:product_variants,id|different:source_variant_id',
            'source_quantity' => 'required|numeric|min:0.01',
            'target_quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        $sourceVariant = ProductVariant::findOrFail($request->source_variant_id);

        if ($sourceVariant->current_stock < $request->source_quantity) {
            return back()->with('error', 'Stock insuficiente del producto origen para realizar la transformación.');
        }

        try {
            DB::beginTransaction();

            // 1. Deduct Stock from Source
            $sourceVariant->decrement('current_stock', $request->source_quantity);

            InventoryMovement::create([
                'product_variant_id' => $sourceVariant->id,
                'type' => 'production_out', // Or specific 'transformation_out' if valid
                'quantity' => -$request->source_quantity,
                'description' => 'Transformación a: ' . ProductVariant::find($request->target_variant_id)->product->name,
                'user_id' => Auth::id(),
            ]);

            // 2. Add Stock to Target
            $targetVariant = ProductVariant::findOrFail($request->target_variant_id);
            $targetVariant->increment('current_stock', $request->target_quantity);

            InventoryMovement::create([
                'product_variant_id' => $targetVariant->id,
                'type' => 'production_in', // Or specific 'transformation_in'
                'quantity' => $request->target_quantity,
                'description' => 'Transformación desde: ' . $sourceVariant->product->name,
                'user_id' => Auth::id(),
            ]);

            // 3. Record Transformation
            ProductTransformation::create([
                'source_variant_id' => $request->source_variant_id,
                'target_variant_id' => $request->target_variant_id,
                'source_quantity' => $request->source_quantity,
                'target_quantity' => $request->target_quantity,
                'user_id' => Auth::id(),
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('inventory.transformations.create')
                ->with('success', 'Transformación registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la transformación: ' . $e->getMessage());
        }
    }
}
