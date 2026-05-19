<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_transaction_creates_order_and_reduces_stock()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'type' => 'finished'
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Regular',
            'price' => 10.00,
            'current_stock' => 50,
            'stock_track' => true,
        ]);

        // 2. Act
        $response = $this->actingAs($user)->postJson(route('pos.store'), [
            'customer_id' => null,
            'items' => [
                [
                    'variant_id' => $variant->id,
                    'quantity' => 5
                ]
            ]
        ]);

        // 3. Assert Response
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // 4. Assert Database
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total' => 50.00, // 5 * 10
            'status' => 'completed'
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 5,
            'price' => 10.00,
            'subtotal' => 50.00
        ]);

        // 5. Assert Stock Reduced
        $this->assertEquals(45, $variant->fresh()->current_stock);
    }

    public function test_pos_transaction_fails_with_insufficient_stock()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'type' => 'finished'
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Regular',
            'price' => 10.00,
            'current_stock' => 5, // Only 5
            'stock_track' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('pos.store'), [
            'items' => [
                [
                    'variant_id' => $variant->id,
                    'quantity' => 10 // Requesting 10
                ]
            ]
        ]);

        $response->assertStatus(400); // Expect Error
        $this->assertEquals(5, $variant->fresh()->current_stock); // Stock unchanged
    }
}
