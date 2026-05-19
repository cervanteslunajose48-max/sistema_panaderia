<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supply;
use App\Models\Warehouse;
use App\Models\SupplyStock;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_flow_deducts_ingredients_and_adds_product_stock()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        Warehouse::create(['id' => 1, 'name' => 'Main']); // Ensure Warehouse 1 exists

        // Create Supply (Flour)
        $flour = Supply::create(['name' => 'Harina', 'base_unit' => 'kg', 'cost' => 2.0]);
        // Restock Supply (+10kg)
        SupplyStock::create([
            'supply_id' => $flour->id,
            'warehouse_id' => 1,
            'quantity' => 10.0
        ]);

        // Create Product (Bread)
        $category = Category::create(['name' => 'Bakery', 'slug' => 'bakery']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Pan', 'slug' => 'pan', 'type' => 'finished']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'name' => 'Unit', 'price' => 1.0, 'stock_track' => true, 'current_stock' => 0]);

        // Create Recipe (1 Bread = 0.5kg Flour)
        $recipe = Recipe::create(['product_variant_id' => $variant->id, 'name' => 'Standard', 'yield_quantity' => 1]);
        $recipe->ingredients()->create(['supply_id' => $flour->id, 'quantity' => 0.5, 'unit' => 'kg']);

        // 2. Act: Produce 5 Breads
        $response = $this->actingAs($user)->post(route('production.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 5
        ]);

        // 3. Assert Response
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('success');

        // 4. Assert Product Stock Increased (+5)
        $this->assertEquals(5, $variant->fresh()->current_stock);

        // 5. Assert Supply Stock Reduced (10 - (5 * 0.5) = 7.5kg)
        $this->assertEquals(7.5, $flour->stocks()->first()->quantity);
    }

    public function test_production_fails_without_sufficient_stock()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        Warehouse::create(['id' => 1, 'name' => 'Main']);

        $flour = Supply::create(['name' => 'Harina', 'base_unit' => 'kg', 'cost' => 2.0]);
        // Only 1kg Stock
        SupplyStock::create(['supply_id' => $flour->id, 'warehouse_id' => 1, 'quantity' => 1.0]);

        $category = Category::create(['name' => 'Bakery', 'slug' => 'bakery']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Pan', 'slug' => 'pan', 'type' => 'finished']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'name' => 'Unit', 'price' => 1.0, 'stock_track' => true, 'current_stock' => 0]);

        $recipe = Recipe::create(['product_variant_id' => $variant->id, 'name' => 'Standard', 'yield_quantity' => 1]);
        $recipe->ingredients()->create(['supply_id' => $flour->id, 'quantity' => 0.5, 'unit' => 'kg']);

        // 2. Act: Try to Produce 10 Breads (Needs 5kg, only have 1kg)
        $response = $this->actingAs($user)->post(route('production.store'), [
            'product_variant_id' => $variant->id,
            'quantity' => 10
        ]);

        // 3. Assert Error
        $response->assertSessionHas('error');

        // Stock Unchanged
        $this->assertEquals(0, $variant->fresh()->current_stock);
        $this->assertEquals(1.0, $flour->stocks()->first()->quantity);
    }
}
