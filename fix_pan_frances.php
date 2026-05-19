<?php

use App\Models\Product;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = Product::find(1);
if ($product) {
    echo "Current Status: " . $product->status . "\n";
    $product->status = 'active';
    $product->save();
    echo "New Status: " . $product->status . "\n";
    echo "Fixed successfully.\n";
} else {
    echo "Product 1 not found.\n";
}
