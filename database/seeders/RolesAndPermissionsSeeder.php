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

        // Users
        Permission::create([
            'name' => 'users.index',
            'module' => 'Users',
            'description' => 'List users.',
        ]);

        Permission::create([
            'name' => 'users.store',
            'module' => 'Users',
            'description' => 'Create users.',
        ]);

        Permission::create([
            'name' => 'users.update',
            'module' => 'Users',
            'description' => 'Update users info.',
        ]);

        Permission::create([
            'name' => 'users.delete',
            'module' => 'Users',
            'description' => 'Delete users.',
        ]);

        Permission::create([
            'name' => 'users.toggle',
            'module' => 'Users',
            'description' => 'Ban & unban users.',
        ]);

        Permission::create([
            'name' => 'users.assign-role',
            'module' => 'Users',
            'description' => 'Assign roles to users.',
        ]);

        // Roles
        Permission::create([
            'name' => 'roles.index',
            'module' => 'Roles & Permissions',
            'description' => 'List roles',
        ]);

        Permission::create([
            'name' => 'roles.store',
            'module' => 'Roles & Permissions',
            'description' => 'Create roles',
        ]);

        Permission::create([
            'name' => 'roles.update',
            'module' => 'Roles & Permissions',
            'description' => 'Update roles',
        ]);

        Permission::create([
            'name' => 'roles.delete',
            'module' => 'Roles & Permissions',
            'description' => 'Delete roles',
        ]);
    }
}
