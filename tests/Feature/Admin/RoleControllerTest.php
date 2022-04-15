<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function a_user_with_permissions_can_get_roles_list()
    {
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

        $this->assertEquals(10, $response->json()['meta']['per_page']);
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_get_the_roles_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson('/api/admin/roles')
        ->assertForbidden();
    }

    /** @test */
    public function a_user_with_permissions_can_create_a_role()
    {
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

        $this->assertTrue(Role::find($data['id'])->hasAllPermissions($permissions));
    }

    /** @test */
    public function role_cannot_be_stored_with_duplicated_name()
    {
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

        $this->assertTrue(Role::where('name', 'Test')->count() === 1);
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_create_a_role()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->postJson('/api/admin/roles', [
            'name' => 'Test',
            'permissions' => Permission::take(2)->pluck('id'),
        ])
        ->assertForbidden();

        $this->assertDatabaseMissing('roles', [
            'name' => 'Test',
        ]);
    }

    /** @test */
    public function a_user_with_permissions_can_update_a_role()
    {
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

        $this->assertTrue(
            2 === $role->fresh()->permissions->count() &&
            $role->hasAllPermissions($permissions->take(2))
        );
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_update_a_role()
    {
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

        $this->assertTrue(
            1 === $role->fresh()->permissions->count() &&
            $role->hasAllPermissions($permissions->first())
        );
    }

    /** @test */
    public function a_user_with_permissions_can_delete_a_role()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $user->assignRole('Super Admin');

        $role = Role::create(['name' => 'Test', 'guard_name' => 'web']);

        $this->deleteJson("/api/admin/roles/{$role->id}")
        ->assertStatus(200)
        ->assertJson([
            'status' => 'OK'
        ]);

        $this->assertModelMissing($role);
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_delete_a_role()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $role = Role::create(['name' => 'Test', 'guard_name' => 'web']);

        $this->deleteJson("/api/admin/roles/{$role->id}")
        ->assertForbidden();

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /** @test */
    public function super_admin_role_cannot_be_updated()
    {
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

        $this->assertTrue($role->permissions()->count() === 0);
    }

    /** @test */
    public function super_admin_role_cannot_be_deleted()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $role = Role::where('name', 'Super Admin')->first();

        $this->deleteJson("/api/admin/roles/{$role->id}")
        ->assertForbidden();

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }
}
