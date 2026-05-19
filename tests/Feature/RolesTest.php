<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the seeder to setup roles
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_cashier_can_access_pos_but_not_reports()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        // Can Access POS
        $this->actingAs($cashier)->get(route('pos.index'))->assertStatus(200);

        // Cannot Access Reports
        $this->actingAs($cashier)->get(route('reports.index'))->assertStatus(403);
    }

    public function test_manager_can_access_reports_but_not_pos()
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        // Can Access Reports
        $this->actingAs($manager)->get(route('reports.index'))->assertStatus(200);

        // Cannot Access POS
        $this->actingAs($manager)->get(route('pos.index'))->assertStatus(403);
    }

    public function test_admin_can_access_everything()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('pos.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('reports.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('users.index'))->assertStatus(200);
    }

    public function test_baker_can_manage_production_but_not_users()
    {
        $baker = User::factory()->create();
        $baker->assignRole('baker');

        // Can Access Production
        $this->actingAs($baker)->get(route('production.create'))->assertStatus(200);

        // Cannot Access Users
        $this->actingAs($baker)->get(route('users.index'))->assertStatus(403);
    }
}
