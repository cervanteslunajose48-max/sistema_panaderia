<?php

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\Customer;
use Illuminate\Support\Str;

echo "--- VALIDATION STARTED ---\n";

try {
    // 1. Validate Category
    echo "\n[Category] Testing...\n";
    $cat = Category::create(['name' => 'Test Cat ' . Str::random(5), 'slug' => 'test-cat-' . Str::random(5), 'description' => 'Initial Desc']);
    echo "Created: {$cat->name} (Status: {$cat->status})\n";

    $cat->update(['description' => 'Updated Desc']);
    $cat = $cat->fresh();
    if ($cat->description !== 'Updated Desc')
        throw new Exception("Category Update Failed");
    echo "Updated: Description OK\n";

    $cat->status = false;
    $cat->save();
    echo "Toggled Off: " . ($cat->status ? 'FAIL' : 'OK') . "\n";

    $cat->status = true;
    $cat->save();
    echo "Toggled On: " . ($cat->status ? 'OK' : 'FAIL') . "\n";

    // 2. Validate Supplier
    echo "\n[Supplier] Testing...\n";
    $sup = Supplier::create(['name' => 'Test Sup ' . Str::random(5), 'phone' => '123']);
    echo "Created: {$sup->name} (Status: {$sup->status})\n";

    $sup->status = false;
    $sup->save();
    echo "Toggled Off: " . ($sup->status ? 'FAIL' : 'OK') . "\n";

    // 3. Validate Supply
    echo "\n[Supply] Testing...\n";
    $supply = Supply::create(['name' => 'Test Supply ' . Str::random(5), 'base_unit' => 'kg', 'cost' => 10.5, 'min_stock' => 5]);
    echo "Created: {$supply->name} (Status: {$supply->status})\n";

    $supply->status = !$supply->status; // Toggle to false (default true)
    $supply->save();
    echo "Toggled Off: " . ($supply->status ? 'FAIL' : 'OK') . "\n";

    // 4. Validate Customer
    echo "\n[Customer] Testing...\n";
    $cust = Customer::create(['name' => 'Test Cust ' . Str::random(5)]);
    echo "Created: {$cust->name} (Status: {$cust->status})\n";

    $cust->status = false;
    $cust->save();
    echo "Toggled Off: " . ($cust->status ? 'FAIL' : 'OK') . "\n";

    echo "\n--- VALIDATION SUCCESSFUL ---\n";

} catch (\Exception $e) {
    echo "\n--- VALIDATION FAILED ---\n";
    echo $e->getMessage() . "\n";
}
