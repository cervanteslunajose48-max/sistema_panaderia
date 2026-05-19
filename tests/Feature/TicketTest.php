<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_page_loads()
    {
        $user = User::factory()->create();

        // Setup Order Data
        $category = Category::create(['name' => 'Bakery', 'slug' => 'bakery']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Pan', 'slug' => 'pan', 'type' => 'finished']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'name' => 'Unit', 'price' => 1.0]);

        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'total' => 10.00,
        ]);

        $order->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 10,
            'price' => 1.0,
            'subtotal' => 10.0
        ]);

        // Act
        $response = $this->actingAs($user)->get(route('orders.ticket', $order));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('PANADERÍA &amp; PASTELERÍA');
        $response->assertSee('Orden: #' . str_pad($order->id, 6, '0', STR_PAD_LEFT));
        $response->assertSee('$10.00');
    }
}
