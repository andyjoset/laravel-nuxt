<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('a user with permissions can get roles list', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $user->assignRole('Super Admin');

    $response = $this->getJson('/api/admin/roles')
    ->assertStatus(200)
    ->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'permissions' => [
                    '*' => [
                        'id',
                        'module',
                        'description',
                    ]
                ],
            ]
        ],
        'meta' => [
            'current_page',
            'from',
            'last_page',
            'path',
            'per_page',
            'to',
            'total',
        ],
        'links',
    ]);

    expect($response->json()['meta']['per_page'])->toEqual(10);
});

test('a user with no permissions cannot get the roles list', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $this->getJson('/api/admin/roles')
    ->assertForbidden();
});

test('a user with permissions can create a role', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $user->assignRole('Super Admin');

    $permissions = Permission::take(4)->get();

    $response = $this->postJson('/api/admin/roles', [
        'name' => 'Test',
        'permissions' => $permissions->pluck('id'),
    ])
    ->assertCreated()
    ->assertJsonStructure([
        'id',
        'name',
        'permissions' => [
            '*' => [
                'id',
                'description',
            ]
        ],
    ]);

    $data = $response->json();
    $this->assertDatabaseHas('roles', [
        'id' => $data['id'],
        'name' => $data['name'],
    ]);

    expect(Role::find($data['id'])->hasAllPermissions($permissions))->toBeTrue();
});

test('role cannot be stored with duplicated name', function () {
    Sanctum::actingAs($user = User::factory()->create());

    Role::create(['name' => 'Test', 'guard_name' => 'web']);

    $this->postJson('/api/admin/roles', $form = [
        'name' => 'Test',
        'permissions' => [],
    ])
    ->assertStatus(422)
    ->assertJsonStructure([
        'message',
        'errors' => ['name']
    ]);

    expect(Role::where('name', 'Test')->count() === 1)->toBeTrue();
});

test('a user with no permissions cannot create a role', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $this->postJson('/api/admin/roles', [
        'name' => 'Test',
        'permissions' => Permission::take(2)->pluck('id'),
    ])
    ->assertForbidden();

    $this->assertDatabaseMissing('roles', [
        'name' => 'Test',
    ]);
});

test('a user with permissions can update a role', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $user->assignRole('Super Admin');

    $permissions = Permission::take(4)->get();

    $role = Role::create(['name' => 'Test', 'guard_name' => 'web'])->givePermissionTo($permissions);

    $response = $this->putJson("/api/admin/roles/{$role->id}", [
        'name' => 'New Role Name',
        'permissions' => $permissions->take(2)->pluck('id'),
    ])
    ->assertStatus(200)
    ->assertJsonStructure([
        'id',
        'name',
        'permissions' => [
            '*' => [
                'id',
                'description',
            ]
        ],
    ]);

    $data = $response->json();
    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => $data['name'],
    ]);

    expect(2 === $role->fresh()->permissions->count() &&
    $role->hasAllPermissions($permissions->take(2)))->toBeTrue();
});

test('a user with no permissions cannot update a role', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $permissions = Permission::take(4)->get();

    $role = Role::create(['name' => 'Test', 'guard_name' => 'web'])->givePermissionTo($permissions->first());

    $response = $this->putJson("/api/admin/roles/{$role->id}", [
        'name' => 'New Role Name',
        'permissions' => $permissions->pluck('id'),
    ])
    ->assertForbidden();

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => $role->name,
    ]);

    expect(1 === $role->fresh()->permissions->count() &&
    $role->hasAllPermissions($permissions->first()))->toBeTrue();
});

test('a user with permissions can delete a role', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $user->assignRole('Super Admin');

    $role = Role::create(['name' => 'Test', 'guard_name' => 'web']);

    $this->deleteJson("/api/admin/roles/{$role->id}")
    ->assertStatus(200)
    ->assertJson([
        'status' => 'OK'
    ]);

    $this->assertModelMissing($role);
});

test('a user with no permissions cannot delete a role', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $role = Role::create(['name' => 'Test', 'guard_name' => 'web']);

    $this->deleteJson("/api/admin/roles/{$role->id}")
    ->assertForbidden();

    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});

test('super admin role cannot be updated', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $role = Role::where('name', 'Super Admin')->first();

    $this->putJson("/api/admin/roles/{$role->id}", [
        'name' => 'New Name',
        'permissions' => Permission::take(3)->pluck('id')
    ])
    ->assertForbidden();

    $this->assertDatabaseHas('roles', [
        'name' => $role->fresh()->name,
    ]);

    expect($role->permissions()->count() === 0)->toBeTrue();
});

test('super admin role cannot be deleted', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $role = Role::where('name', 'Super Admin')->first();

    $this->deleteJson("/api/admin/roles/{$role->id}")
    ->assertForbidden();

    $this->assertDatabaseHas('roles', ['id' => $role->id]);
});
