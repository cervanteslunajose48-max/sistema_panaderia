<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Supply;
use App\Models\Warehouse;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\InventoryMovement;

class PopulateDashboardData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-dashboard-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean database (except users/settings) and seed with 10 records per module for dashboard testing.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning database...');

        // For Postgres, we can try to disable constraints
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('SET session_replication_role = \'replica\';');
        } else {
            Schema::disableForeignKeyConstraints();
        }

        // Child tables first, then parents
        $tables = [
            'order_items', 'cash_movements', 'orders', // sales and items
            'purchase_items', 'purchases', // purchases
            'recipe_ingredients', 'recipes', // recipes
            'inventory_movements', 'special_order_items', 'special_orders', 'product_images', 'product_variants', 'products', // product related
            'categories', 'customers', 'suppliers', 'supply_stocks', 'supplies', 'warehouses', 'cash_registers' // base tables
        ];

        foreach ($tables as $table) {
            DB::table($table)->delete();
        }

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('SET session_replication_role = \'origin\';');
        } else {
            Schema::enableForeignKeyConstraints();
        }

        $this->info('Seeding modules (10 records each)...');

        // 1. Categories
        $categories = [];
        for ($i = 1; $i <= 10; $i++) {
            $categories[] = Category::create([
                'name' => "Categoría Demo $i",
                'slug' => "categoria-demo-$i",
                'description' => "Descripción de la categoría de prueba $i",
                'status' => true
            ]);
        }

        // 2. Warehouses
        $warehouses = [];
        for ($i = 1; $i <= 10; $i++) {
            $warehouses[] = Warehouse::create([
                'name' => "Almacén Demo $i",
                'location' => "Ubicación de prueba $i",
                'is_active' => true
            ]);
        }

        // 3. Suppliers
        $suppliers = [];
        for ($i = 1; $i <= 10; $i++) {
            $suppliers[] = Supplier::create([
                'name' => "Proveedor Demo $i",
                'contact_name' => "Contacto $i",
                'phone' => "99988877$i",
                'email' => "proveedor$i@demo.com",
                'is_active' => true
            ]);
        }

        // 4. Products & Variants
        $products = [];
        $variants = [];
        foreach ($categories as $index => $cat) {
            $product = Product::create([
                'category_id' => $cat->id,
                'name' => "Producto Demo " . ($index + 1),
                'slug' => "producto-demo-" . ($index + 1),
                'description' => "Descripción del producto de prueba " . ($index + 1),
                'type' => 'finished',
                'status' => 'active'
            ]);
            $products[] = $product;

            $variants[] = ProductVariant::create([
                'product_id' => $product->id,
                'name' => 'Unidad',
                'sku' => "SKU-DEMO-" . ($index + 1),
                'price' => rand(10, 100) / 2,
                'stock_track' => true
            ]);
        }

        // 5. Supplies
        $supplies = [];
        for ($i = 1; $i <= 10; $i++) {
            $supplies[] = Supply::create([
                'name' => "Insumo Demo $i",
                'code' => "INS-DEMO-$i",
                'unit' => ['kg', 'litro', 'unidad'][rand(0, 2)],
                'cost' => rand(5, 50),
                'stock' => rand(100, 500),
                'min_stock' => 50,
                'is_active' => true
            ]);
        }

        // 6. Customers
        $customers = [];
        for ($i = 1; $i <= 10; $i++) {
            $customers[] = Customer::create([
                'name' => "Cliente Demo $i",
                'document_type' => 'DNI',
                'document_number' => '7000000' . $i,
                'email' => "cliente$i@demo.com",
                'phone' => '99911122' . $i,
                'is_active' => true
            ]);
        }

        // 7. Recipes
        for ($i = 0; $i < 10; $i++) {
            $recipe = Recipe::create([
                'product_id' => $products[$i]->id,
                'name' => "Receta para " . $products[$i]->name,
                'yield' => rand(10, 50),
                'instructions' => "Instrucciones de prueba para la receta $i."
            ]);

            // Add 2 items per recipe
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'supply_id' => $supplies[rand(0, 4)]->id,
                'quantity' => rand(1, 5)
            ]);
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'supply_id' => $supplies[rand(5, 9)]->id,
                'quantity' => rand(1, 5)
            ]);
        }

        // 8. Purchases
        $user = \App\Models\User::first();
        for ($i = 1; $i <= 10; $i++) {
            $purchase = Purchase::create([
                'supplier_id' => $suppliers[$i - 1]->id,
                'warehouse_id' => $warehouses[0]->id,
                'user_id' => $user->id,
                'purchase_date' => now()->subDays(rand(1, 30)),
                'status' => 'received',
                'total_amount' => rand(100, 1000)
            ]);

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'supply_id' => $supplies[$i - 1]->id,
                'quantity' => rand(10, 50),
                'unit_cost' => rand(5, 20),
                'total_cost' => rand(50, 1000)
            ]);
        }

        // 9. Orders (Sales)
        for ($i = 1; $i <= 10; $i++) {
            $total = rand(20, 200);
            $daysAgo = ($i % 7); // Distribute orders across the last 7 days
            $order = Order::create([
                'user_id' => $user->id,
                'customer_id' => $customers[$i - 1]->id,
                'type' => ['Boleta', 'Factura', 'Ticket'][rand(0, 2)],
                'total' => $total,
                'status' => 'completed',
            ]);

            DB::table('orders')->where('id', $order->id)->update([
                'created_at' => now()->subDays($daysAgo),
                'updated_at' => now()->subDays($daysAgo),
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_id' => $variants[$i - 1]->id,
                'quantity' => rand(1, 5),
                'price' => $variants[$i - 1]->price,
                'subtotal' => $total
            ]);
        }

        $this->info('Successfully seeded 10 records per module!');
        $this->info('You can now view the dashboard with realistic data.');
    }
}
