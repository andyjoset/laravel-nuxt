<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommonControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function can_retrive_all_roles_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $user->assignRole('Super Admin');

        $this->getJson('/api/roles')
        ->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'id',
                'name',
            ]
        ]);
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_retrive_all_roles_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson('/api/roles')
        ->assertForbidden();
    }

    /** @test */
    public function can_retrive_all_permissions_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $user->assignRole('Super Admin');

        $this->getJson('/api/permissions')
        ->assertStatus(200)
        ->assertJsonStructure([
            '*' => [
                'id',
                'module',
                'description',
            ]
        ]);
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_retrive_all_permissions_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson('/api/roles')
        ->assertForbidden();
    }
}
