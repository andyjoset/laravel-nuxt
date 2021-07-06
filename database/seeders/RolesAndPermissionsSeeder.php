<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'Super Admin']);

        Permission::create(['name' => 'users.index']);
        Permission::create(['name' => 'users.store']);
        Permission::create(['name' => 'users.update']);
        Permission::create(['name' => 'users.delete']);
        Permission::create(['name' => 'users.toggle']);
    }
}
