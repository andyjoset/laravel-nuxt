<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('can retrive all roles list', function () {
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
});

test('a user with no permissions cannot retrive all roles list', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $this->getJson('/api/roles')
    ->assertForbidden();
});

test('can retrive all permissions list', function () {
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
});

test('a user with no permissions cannot retrive all permissions list', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $this->getJson('/api/roles')
    ->assertForbidden();
});
