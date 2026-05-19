<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_page_loads_and_calculates_sales()
    {
        // 1. Setup Data
        $user = User::factory()->create();

        // Create Sales Data for Today
        Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'total' => 100.00,
            'created_at' => Carbon::now()
        ]);

        Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
            'total' => 50.00,
            'created_at' => Carbon::now()
        ]);

        // 2. Act
        $response = $this->actingAs($user)->get(route('reports.index'));

        // 3. Assert Response
        $response->assertStatus(200);
        $response->assertViewHas('chartLabels');
        $response->assertViewHas('chartValues');

        // Check if view data contains aggregated total 150
        $chartValues = $response->viewData('chartValues');
        $this->assertTrue($chartValues->contains(150.00));
    }
}
