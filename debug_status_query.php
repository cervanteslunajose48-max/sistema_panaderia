<?php

use App\Models\Product;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = Product::where('status', 'active')->count();
echo "Products with status 'active': " . $count . "\n";

$countInt = Product::where('status', 1)->count();
echo "Products with status 1: " . $countInt . "\n";

$product = Product::find(1);
echo "Product 1 status: " . $product->status . " (Type: " . gettype($product->status) . ")\n";
