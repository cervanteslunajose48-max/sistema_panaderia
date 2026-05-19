<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(10);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'type' => 'required|in:finished,service,raw_material',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.current_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']) . '-' . uniqid(),
                'description' => $validated['description'],
                'type' => $validated['type'],
                'status' => 'active',
            ]);

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => true,
                ]);
            }

            foreach ($validated['variants'] as $variantData) {
                $product->variants()->create([
                    'name' => $variantData['name'],
                    'price' => $variantData['price'],
                    'current_stock' => $variantData['current_stock'],
                    'sku' => Str::upper(Str::random(8)), // Auto-generate SKU for now
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Producto creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'type' => 'required|in:finished,service,raw_material',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        DB::transaction(function () use ($validated, $request, $product) {
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'type' => $validated['type'],
            ]);

            // Handle Image Upload
            if ($request->hasFile('image')) {
                // Upload new image
                $path = $request->file('image')->store('products', 'public');

                // Get current primary image
                $currentImage = $product->primaryImage;

                if ($currentImage) {
                    // Delete old file from storage
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($currentImage->image_path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($currentImage->image_path);
                    }

                    // Update existing record or delete and create new
                    $currentImage->update([
                        'image_path' => $path,
                    ]);
                } else {
                    // Create new primary image
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => true,
                    ]);
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Producto actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // $product->delete(); // Cascades to variants
        // return redirect()->route('products.index')->with('success', 'Producto eliminado con éxito.');
        return back()->with('error', 'La eliminación está deshabilitada. Use la opción Activar/Desactivar.');
    }

    public function toggleStatus(Product $product)
    {
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();
        $status = $product->status === 'active' ? 'activado' : 'desactivado';
        return back()->with('success', "Producto $status correctamente.");
    }

    /**
     * Upload product image
     */
    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $path = $request->file('image')->store('products', 'public');

        $isFirst = $product->images()->count() === 0;

        \App\Models\ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $path,
            'is_primary' => $isFirst // First image is primary
        ]);

        return back()->with('success', 'Imagen subida correctamente');
    }

    /**
     * Delete product image
     */
    public function deleteImage(\App\Models\ProductImage $image)
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Imagen eliminada');
    }

    /**
     * Set image as primary
     */
    public function setPrimaryImage(\App\Models\ProductImage $image)
    {
        // Remove primary from all images of this product
        \App\Models\ProductImage::where('product_id', $image->product_id)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Imagen principal actualizada');
    }
}
