<?php

use App\Models\InventoryMovement;
use Illuminate\Contracts\Console\Kernel;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

// 1. Get Latest Record
$latest = InventoryMovement::where('type', 'production_in')
    ->with(['productVariant.product.category', 'user'])
    ->latest()
    ->first();

if (!$latest) {
    echo "No production records found!\n";
    exit;
}

echo "Latest Record ID: {$latest->id}\n";
echo "Created At: {$latest->created_at} (Timestamp: " . $latest->created_at->timestamp . ")\n";
echo "Microtime: " . $latest->created_at->format('Y-m-d H:i:s.u') . "\n";
echo "Quantity: {$latest->quantity}\n";
echo "User ID: " . ($latest->user_id ?? 'NULL') . "\n";
echo "Product ID: " . ($latest->productVariant->product_id ?? 'NULL') . "\n";
echo "Category ID: " . ($latest->productVariant->product->category_id ?? 'NULL') . "\n";

// 2. Test Filters
$startDateStr = '2026-02-01'; // From screenshot
$endDateStr = '2026-02-05';   // From screenshot

$start = Carbon::parse($startDateStr)->startOfDay();
$end = Carbon::parse($endDateStr)->endOfDay();

echo "\nFilter Check:\n";
echo "Start Filter: " . $start->format('Y-m-d H:i:s') . "\n";
echo "End Filter:   " . $end->format('Y-m-d H:i:s') . "\n";

$isDateMatch = $latest->created_at->between($start, $end);
echo "Date Match? " . ($isDateMatch ? "YES" : "NO") . "\n";

if (!$isDateMatch) {
    echo "  -> Record is " . ($latest->created_at->gt($end) ? "AFTER" : "BEFORE") . " range.\n";
}

// 3. Test Query Count
$count = InventoryMovement::where('type', 'production_in')
    ->whereBetween('created_at', [$start, $end])
    ->count();

echo "Query Count in Range: $count\n";
