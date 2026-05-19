<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modules
    Route::get('inventory/export', [InventoryController::class, 'exportCsv'])->name('inventory.export');
    Route::get('inventory/print', [InventoryController::class, 'print'])->name('inventory.print');
    Route::resource('inventory', InventoryController::class);
    Route::post('inventory/{inventory}/toggle', [InventoryController::class, 'toggleStatus'])->name('inventory.toggle'); // Added by edit
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/toggle', [ProductController::class, 'toggleStatus'])->name('products.toggle'); // Added by edit
    Route::post('products/{product}/upload-image', [ProductController::class, 'uploadImage'])->name('products.upload-image');
    Route::delete('product-images/{image}', [ProductController::class, 'deleteImage'])->name('product-images.delete');
    Route::post('product-images/{image}/set-primary', [ProductController::class, 'setPrimaryImage'])->name('product-images.set-primary');


    // Inventory & Suppliers // Added by edit
    Route::resource('suppliers', SupplierController::class); // Modified by edit (moved and namespace removed)
    Route::post('suppliers/{supplier}/toggle', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle'); // Added by edit

    Route::get('supplies/search', [SupplyController::class, 'search'])->name('supplies.search'); // Added for Recipe Autocomplete
    Route::resource('supplies', SupplyController::class); // Modified by edit (moved and namespace removed)
    Route::post('supplies/{supply}/toggle', [SupplyController::class, 'toggleStatus'])->name('supplies.toggle'); // Added by edit

    Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class)->middleware(['permission:manage inventory']);

    Route::resource('production', ProductionController::class)->only(['create', 'store', 'index']); // Added index // Modified by edit


    // Clients & Configuration (Phase 10)
    Route::resource('customers', CustomerController::class); // Modified by edit (namespace removed)
    Route::post('customers/{customer}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle'); // Added by edit

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index'); // Modified by edit (namespace removed)
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update'); // Modified by edit (namespace removed)

    // Production & Supplies // This section is now partially redundant due to reordering above.
    // The original lines below are removed as they are replaced/moved by the edit.
    // Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->middleware(['permission:manage inventory']); // Removed by edit
    // Route::resource('supplies', SupplyController::class)->middleware(['permission:manage inventory']); // Removed by edit
    Route::post('supplies/{supply}/restock', [SupplyController::class, 'restock'])
        ->middleware(['permission:manage inventory'])
        ->name('supplies.restock');

    Route::resource('recipes', RecipeController::class)->middleware(['permission:manage production']);
    Route::post('recipes/{recipe}/duplicate', [RecipeController::class, 'duplicate'])
        ->middleware(['permission:manage production'])
        ->name('recipes.duplicate');

    // Users (Admin Only)
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware(['role:admin']);
    Route::post('users/{user}/toggle', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->middleware(['role:admin'])->name('users.toggle');

    // Reports (Manager & Admin)
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])
        ->middleware(['permission:view reports'])
        ->name('reports.index');

    Route::get('reports/production/export', [\App\Http\Controllers\ReportController::class, 'exportProductionCsv'])
        ->middleware(['permission:view reports'])
        ->name('reports.production.export');

    Route::get('reports/production', [\App\Http\Controllers\ReportController::class, 'production'])
        ->middleware(['permission:view reports'])
        ->name('reports.production');

    // Production (Baker & Admin)
    Route::get('production', [ProductionController::class, 'create'])->middleware(['permission:manage production'])->name('production.create');
    Route::get('production/products', [ProductionController::class, 'getProducts'])->middleware(['permission:manage production'])->name('production.products');
    Route::post('production', [ProductionController::class, 'store'])->middleware(['permission:manage production'])->name('production.store');
    Route::post('production/batch', [ProductionController::class, 'batchStore'])->middleware(['permission:manage production'])->name('production.batch.store');

    // POS (Cashier & Admin)
    Route::get('/pos', [PosController::class, 'index'])->middleware(['permission:execute pos'])->name('pos.index');
    Route::post('/pos/store', [PosController::class, 'store'])->middleware(['permission:execute pos'])->name('pos.store');
    Route::get('/orders/{order}/ticket', [\App\Http\Controllers\TicketController::class, 'show'])->name('orders.ticket');
    Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show']);

    // Suppliers
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::post('suppliers/{supplier}/toggle', [\App\Http\Controllers\SupplierController::class, 'toggleStatus'])->name('suppliers.toggle');

    // Purchases
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
    Route::post('purchases/{purchase}/receive', [\App\Http\Controllers\PurchaseController::class, 'receive'])->name('purchases.receive');

    // Inventory Adjustments
    Route::get('inventory/adjustments/create', [\App\Http\Controllers\InventoryAdjustmentController::class, 'create'])->name('inventory.adjustments.create');
    Route::post('inventory/adjustments', [\App\Http\Controllers\InventoryAdjustmentController::class, 'store'])->name('inventory.adjustments.store');

    // Warehouses
    Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class);
    Route::post('warehouses/{warehouse}/toggle', [\App\Http\Controllers\WarehouseController::class, 'toggleStatus'])->name('warehouses.toggle');

    // Supplies
    Route::resource('supplies', SupplyController::class);
    Route::post('supplies/{supply}/toggle', [SupplyController::class, 'toggleStatus'])->name('supplies.toggle');

    // Cash Registers (Phase 12)
    Route::resource('cash-registers', \App\Http\Controllers\CashRegisterController::class);
    Route::post('cash-registers/{cash_register}/close', [\App\Http\Controllers\CashRegisterController::class, 'close'])->name('cash-registers.close');
    Route::post('cash-registers/{cash_register}/movement', [\App\Http\Controllers\CashRegisterController::class, 'storeMovement'])->name('cash-registers.movement');
});


// Product Transformations
Route::get('inventory/transformations/create', [\App\Http\Controllers\ProductTransformationController::class, 'create'])->name('inventory.transformations.create');
Route::post('inventory/transformations', [\App\Http\Controllers\ProductTransformationController::class, 'store'])->name('inventory.transformations.store');

// Special Orders
Route::resource('special-orders', \App\Http\Controllers\SpecialOrderController::class);
Route::patch('special-orders/{special_order}/status', [\App\Http\Controllers\SpecialOrderController::class, 'updateStatus'])->name('special-orders.update-status');

require __DIR__ . '/auth.php';
