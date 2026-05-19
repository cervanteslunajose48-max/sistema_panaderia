<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'manage inventory']);
        Permission::create(['name' => 'manage production']);
        Permission::create(['name' => 'execute pos']);

        // create roles and assign created permissions

        // Baker
        $role = Role::create(['name' => 'baker']);
        $role->givePermissionTo(['manage production', 'manage inventory']);

        // Cashier
        $role = Role::create(['name' => 'cashier']);
        $role->givePermissionTo('execute pos');

        // Manager
        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo(['view reports', 'manage inventory']);

        // Admin
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        // Assign Admin role to first user if exists
        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
