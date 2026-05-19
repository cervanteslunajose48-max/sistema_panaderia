<?php

use App\Models\InventoryMovement;
use Illuminate\Contracts\Console\Kernel;
use Carbon\Carbon;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

// Simulate Request Inputs
$startDate = '2026-02-01'; // Y-m-d from input type=date
$endDate = '2026-02-05';
$categoryId = 1;
$productId = 1;

echo "Filtering with:\nStart: $startDate\nEnd: $endDate\nCat: $categoryId\nProd: $productId\n\n";

// Replicate Controller Logic
$query = InventoryMovement::where('type', 'production_in')
    ->whereBetween('created_at', [
        Carbon::parse($startDate)->startOfDay(),
        Carbon::parse($endDate)->endOfDay()
    ])
    ->with(['productVariant.product.category', 'user']);

if ($categoryId) {
    echo "Adding Category Filter...\n";
    $query->whereHas('productVariant.product', function ($q) use ($categoryId) {
        $q->where('category_id', $categoryId);
    });
}

if ($productId) {
    echo "Adding Product Filter...\n";
    $query->whereHas('productVariant', function ($q) use ($productId) {
        $q->where('product_id', $productId);
    });
}

// Check SQL
// echo "SQL: " . $query->toSql() . "\n";
// echo "Bindings: " . implode(', ', $query->getBindings()) . "\n";

$movements = $query->get();

echo "Found: " . $movements->count() . "\n";
foreach ($movements as $m) {
    echo "ID: {$m->id}\n";
}
