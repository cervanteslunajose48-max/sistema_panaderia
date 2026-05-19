<?php

// Simple test script to check production module data
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRODUCTION MODULE TEST ===\n\n";

// 1. Check finished products
$products = App\Models\Product::where('type', 'finished')->where('status', true)->get();
echo "1. Finished Products (status=true): " . $products->count() . "\n";
foreach ($products as $product) {
    echo "   - {$product->name} (ID: {$product->id}, Status: " . ($product->status ? 'Active' : 'Inactive') . ")\n";
}

// 2. Check product variants
echo "\n2. Product Variants:\n";
foreach ($products as $product) {
    $variants = $product->variants;
    echo "   Product: {$product->name} has {$variants->count()} variant(s)\n";
    foreach ($variants as $variant) {
        echo "      - {$variant->name} (ID: {$variant->id}, Stock: {$variant->current_stock})\n";
    }
}

// 3. Check recipes
echo "\n3. Recipes:\n";
$recipes = App\Models\Recipe::with('productVariant.product')->get();
echo "   Total recipes: " . $recipes->count() . "\n";
foreach ($recipes as $recipe) {
    echo "   - Recipe for: " . ($recipe->productVariant->product->name ?? 'Unknown') . " - " . ($recipe->productVariant->name ?? 'Unknown') . "\n";
    echo "     Ingredients: " . $recipe->ingredients->count() . "\n";
}

// 4. Test the getProducts endpoint logic
echo "\n4. Testing getProducts Logic:\n";
$controller = new App\Http\Controllers\ProductionController();
try {
    $response = $controller->getProducts();
    $data = json_decode($response->getContent(), true);
    echo "   API Response: " . count($data) . " items returned\n";
    if (count($data) > 0) {
        echo "   First item: " . json_encode($data[0], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   ⚠️ WARNING: API returned empty array!\n";
    }
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
