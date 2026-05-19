<?php

use App\Models\InventoryMovement;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$movements = InventoryMovement::where('type', 'production_in')->take(5)->get();

foreach ($movements as $m) {
    echo "ID: {$m->id} | User ID: " . ($m->user_id ?? 'NULL') . "\n";
    if (!$m->user) {
        echo "User Relation is NULL\n";
    }
}
