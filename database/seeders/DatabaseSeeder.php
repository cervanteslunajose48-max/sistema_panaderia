<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@panaderia.com',
            'password' => bcrypt('password'),
        ]);

        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Create Categories
        $catPanes = Category::create([
            'name' => 'Panes',
            'slug' => 'panes',
            'image' => 'panes.jpg', // Placeholder
        ]);

        $catPasteles = Category::create([
            'name' => 'Pasteles',
            'slug' => 'pasteles',
            'image' => 'pasteles.jpg',
        ]);

        $catBebidas = Category::create([
            'name' => 'Bebidas',
            'slug' => 'bebidas',
            'image' => 'bebidas.jpg',
        ]);

        // 3. Create Products and Variants

        // --- Pan Francés ---
        $panFrances = Product::create([
            'category_id' => $catPanes->id,
            'name' => 'Pan Francés',
            'slug' => 'pan-frances',
            'description' => 'Tradicional pan francés crocante.',
            'type' => 'finished',
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $panFrances->id,
            'name' => 'Unidad',
            'sku' => 'PAN-FRA-001',
            'price' => 0.50,
            'stock_track' => true,
        ]);

        // --- Torta de Chocolate ---
        $tortaChoco = Product::create([
            'category_id' => $catPasteles->id,
            'name' => 'Torta de Chocolate',
            'slug' => 'torta-chocolate',
            'description' => 'Deliciosa torta de chocolate con fudge.',
            'type' => 'finished',
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $tortaChoco->id,
            'name' => 'Entera',
            'sku' => 'TOR-CHO-ENT',
            'price' => 45.00,
            'stock_track' => true,
        ]);

        ProductVariant::create([
            'product_id' => $tortaChoco->id,
            'name' => 'Porción',
            'sku' => 'TOR-CHO-POR',
            'price' => 6.00,
            'stock_track' => true,
        ]);

        // --- Croissant ---
        $croissant = Product::create([
            'category_id' => $catPanes->id,
            'name' => 'Croissant',
            'slug' => 'croissant',
            'description' => 'Croissant de mantequilla estilo francés.',
            'type' => 'finished',
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $croissant->id,
            'name' => 'Unidad',
            'sku' => 'PAN-CRO-001',
            'price' => 3.50,
            'stock_track' => true,
        ]);
    }
}
