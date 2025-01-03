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

    public function test_can_retrive_all_roles_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $user->assignRole('Super Admin');

        $this->getJson('/api/roles')
        ->assertStatus(200)
        ->assertJsonStructure([
           'data' => [
                '*' => [
                    'id',
                    'name',
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
    }

    public function test_a_user_with_no_permissions_cannot_retrive_all_roles_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson('/api/roles')
        ->assertForbidden();
    }

    public function test_can_retrive_all_permissions_list()
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

    public function test_a_user_with_no_permissions_cannot_retrive_all_permissions_list()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson('/api/roles')
        ->assertForbidden();
    }
}
