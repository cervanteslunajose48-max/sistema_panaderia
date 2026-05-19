<?php

use App\Models\InventoryMovement;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

// Check Movement 14 specifically
$m = InventoryMovement::with(['productVariant.product.category'])->find(14);

if (!$m) {
    echo "Movement 14 not found.\n";
} else {
    echo "Movement ID: {$m->id}\n";
    echo "Type: {$m->type}\n";
    echo "Date: {$m->created_at}\n";

    $v = $m->productVariant;
    if ($v) {
        echo "Variant ID: {$v->id}\n";
        echo "Variant Product ID: {$v->product_id}\n";

        $p = $v->product;
        if ($p) {
            echo "Product ID: {$p->id}\n";
            echo "Product Name: {$p->name}\n";
            echo "Product Category ID: {$p->category_id}\n";

            $c = $p->category;
            if ($c) {
                echo "Category ID: {$c->id}\n";
                echo "Category Name: {$c->name}\n";
            } else {
                echo "Category: NULL\n";
            }
        } else {
            echo "Product: NULL\n";
        }
    } else {
        echo "Variant: NULL\n";
    }
}
