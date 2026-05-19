<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Recipe;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    /**
     * Display a listing of recipes.
     */
    public function index(Request $request)
    {
        $query = Recipe::with(['productVariant.product.primaryImage', 'ingredients.supply']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by product
        if ($request->has('product_id') && $request->product_id) {
            $query->whereHas('productVariant', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->whereHas('productVariant.product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $recipes = $query->latest()->paginate(15);
        $products = \App\Models\Product::all();
        $categories = \App\Models\Category::all();

        return view('recipes.index', compact('recipes', 'products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $variants = ProductVariant::with('product.primaryImage')->get();
        $supplies = Supply::all();
        return view('recipes.create', compact('variants', 'supplies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id|unique:recipes,product_variant_id',
            'name' => 'required|string',
            'yield_quantity' => 'required|numeric|min:1',
            'instructions' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.supply_id' => 'required|exists:supplies,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        try {
            DB::beginTransaction();

            // Recipe is now linked to product variant which has the image
            $recipe = Recipe::create([
                'product_variant_id' => $validated['product_variant_id'],
                'name' => $validated['name'],
                'yield_quantity' => $validated['yield_quantity'],
                'instructions' => $request->instructions,
            ]);

            foreach ($validated['ingredients'] as $ing) {
                $recipe->ingredients()->create([
                    'supply_id' => $ing['supply_id'],
                    'quantity' => $ing['quantity'],
                    // Unit assumed same as base supply unit for now
                ]);
            }

            DB::commit();
            return redirect()->route('recipes.index')->with('success', 'Receta creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe)
    {
        $recipe->load(['ingredients.supply', 'productVariant.product.primaryImage']);
        $variants = ProductVariant::with('product.primaryImage')->get();
        $supplies = Supply::all();
        return view('recipes.edit', compact('recipe', 'variants', 'supplies'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        $recipe->load(['ingredients.supply.stocks', 'productVariant.product.primaryImage']);
        return view('recipes.show', compact('recipe'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'yield_quantity' => 'required|numeric|min:1',
            'instructions' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.supply_id' => 'required|exists:supplies,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        try {
            DB::beginTransaction();

            // Update recipe basic info
            $recipe->update([
                'name' => $validated['name'],
                'yield_quantity' => $validated['yield_quantity'],
                'instructions' => $request->instructions,
            ]);

            // Delete old ingredients and create new ones
            $recipe->ingredients()->delete();

            foreach ($validated['ingredients'] as $ing) {
                $recipe->ingredients()->create([
                    'supply_id' => $ing['supply_id'],
                    'quantity' => $ing['quantity'],
                ]);
            }

            DB::commit();
            return redirect()->route('recipes.index')->with('success', 'Receta actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Duplicate an existing recipe
     */
    public function duplicate(Recipe $recipe)
    {
        try {
            DB::beginTransaction();

            // Create new recipe with duplicated data
            $newRecipe = Recipe::create([
                'product_variant_id' => $recipe->product_variant_id,
                'name' => $recipe->name . ' (Copia)',
                'yield_quantity' => $recipe->yield_quantity,
                'description' => $recipe->description,
                'prep_time' => $recipe->prep_time,
            ]);

            // Duplicate ingredients
            foreach ($recipe->ingredients as $ingredient) {
                $newRecipe->ingredients()->create([
                    'supply_id' => $ingredient->supply_id,
                    'quantity' => $ingredient->quantity,
                ]);
            }

            DB::commit();
            return redirect()->route('recipes.edit', $newRecipe)
                ->with('success', 'Receta duplicada correctamente. Puede editarla ahora.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
