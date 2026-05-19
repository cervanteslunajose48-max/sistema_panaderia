<?php

use App\Models\Product;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = Product::with(['variants', 'category'])
    ->where('name', 'like', '%ancés%') // searching for "Francés" or similar
    ->orWhere('name', 'like', '%frances%')
    ->first();

if ($product) {
    echo "Product Found: " . $product->name . "\n";
    echo "ID: " . $product->id . "\n";
    echo "Status: " . $product->status . "\n";
    echo "Category: " . ($product->category ? $product->category->name . " (ID: " . $product->category->id . ")" : 'None') . "\n";
    echo "Product Category ID: " . $product->category_id . "\n";
    echo "Variants:\n";
    foreach ($product->variants as $variant) {
        echo " - Variant: " . $variant->name . " (ID: " . $variant->id . ")\n";
        echo "   Stock Track: " . ($variant->stock_track ? 'Yes' : 'No') . "\n";
        echo "   Current Stock: " . $variant->current_stock . "\n";
    }
} else {
    echo "Product not found.\n";
}
