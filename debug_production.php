<?php

use App\Models\InventoryMovement;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$movements = InventoryMovement::where('type', 'production_in')
    ->with('productVariant.product')
    ->latest()
    ->take(5)
    ->get();

echo "Count: " . $movements->count() . "\n";
foreach ($movements as $m) {
    echo "ID: {$m->id} | Date: {$m->created_at} | Qty: {$m->quantity} | Product: " . ($m->productVariant->product->name ?? 'N/A') . "\n";
}
