<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use App\Notifications\UserAccountGenerated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('an admin can fetch users list', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $user->assignRole('Super Admin');

    $response = $this->getJson('/api/admin/users')
    ->assertStatus(200)
    ->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
                'email',
                'active',
                'roles' => [
                    '*' => [
                        'id',
                        'name',
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

test('a user with no permissions cannot get the users list', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $this->getJson('/api/admin/users')
    ->assertForbidden();
});

test('an admin can create a user account', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $user->assignRole('Super Admin');

    Notification::fake();

    $response = $this->postJson('/api/admin/users', [
        'name' => 'Test',
        'email' => 'test@example.com',
    ])
    ->assertCreated()
    ->assertJsonStructure([
        'id',
        'name',
        'email',
        'active',
        'roles' => [
            '*' => [
                'id',
                'name',
            ]
        ],
    ]);

    Notification::assertSentTo(
        User::firstWhere('id', $response->json()['id']),
        UserAccountGenerated::class
    );

    $data = $response->json();
    $this->assertDatabaseHas('users', [
        'id' => $data['id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'active' => $data['active'],
    ]);
});

test('an admin can create a user account and assign a role', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $role = Role::create(['name' => 'Test', 'guard_name' => 'web']);

    $user->assignRole('Super Admin');

    Notification::fake();

    $response = $this->postJson('/api/admin/users', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'role_id' => $role->id,
    ])
    ->assertCreated()
    ->assertJsonStructure([
        'id',
        'name',
        'email',
        'active',
        'roles' => [
            '*' => [
                'id',
                'name',
            ]
        ],
    ]);

    Notification::assertSentTo(
        $createdUser = User::firstWhere('id', $response->json()['id']),
        UserAccountGenerated::class
    );

    $data = $response->json();
    $this->assertDatabaseHas('users', [
        'id' => $data['id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'active' => $data['active'],
    ]);

    expect($createdUser->hasRole($role->name))->toBeTrue();
});

test('user cannot be stored with duplicated email', function () {
    Sanctum::actingAs($user = User::factory()->create());

    Notification::fake();

    $this->postJson('/api/admin/users', $form = [
        'name' => 'Test',
        'email' => $user->email,
    ])
    ->assertStatus(422)
    ->assertJsonStructure([
        'message',
        'errors' => ['email']
    ]);

    Notification::assertNothingSent();

    $this->assertDatabaseMissing('users', $form);
});

test('a user with no permissions cannot create a user account', function () {
    Sanctum::actingAs($user = User::factory()->create());

    Notification::fake();

    $response = $this->postJson('/api/admin/users', $form = [
        'name' => 'Test',
        'email' => 'test@example.com',
    ])
    ->assertForbidden();

    Notification::assertNothingSent();

    $this->assertDatabaseMissing('users', $form);
});

test('an admin can update a user account', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $user->assignRole('Super Admin');

    $response = $this->putJson("/api/admin/users/{$users[1]->id}", [
        'name' => 'New name',
        'email' => 'new-email@example.com',
    ])
    ->assertStatus(200)
    ->assertJson([
        'id'    => $users[1]->id,
        'name'  => 'New name',
        'email' => 'new-email@example.com',
    ]);

    $data = $response->json();
    $this->assertDatabaseHas('users', [
        'id' => $data['id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'active' => $data['active'],
    ]);
});

test('an admin can remove a role from a user account', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $user->assignRole('Super Admin');

    $role = Role::create(['name' => 'Test', 'guard_name' => 'web']);
    $users[1]->assignRole($role);

    $response = $this->putJson("/api/admin/users/{$users[1]->id}", [
        'name' => 'New name',
        'email' => $users[1]->email,
        'role_id' => null,
    ])
    ->assertStatus(200)
    ->assertJson([
        'id'    => $users[1]->id,
        'name'  => 'New name',
        'email' => $users[1]->email,
        'roles' => []
    ]);

    $data = $response->json();
    $this->assertDatabaseHas('users', [
        'id' => $data['id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'active' => $data['active'],
    ]);

    expect($users[1]->fresh()->roles()->count() === 0)->toBeTrue();
});

test('user cannot be updated with duplicated email', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $this->putJson("/api/admin/users/{$users[1]->id}", [
        'name' => 'New name',
        'email' => $user->email,
    ])
    ->assertStatus(422)
    ->assertJsonStructure([
        'message',
        'errors' => ['email']
    ]);
});

test('a user with no permissions cannot update a user account', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $this->putJson("/api/admin/users/{$users[1]->id}", $form = [
        'name' => 'New name',
        'email' => 'new-email@example.com',
    ])
    ->assertForbidden();

    $this->assertDatabaseHas('users', $users[1]->toArray());
});

test('an admin can deleted a user account', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $user->assignRole('Super Admin');

    $this->deleteJson("/api/admin/users/{$users[1]->id}")
    ->assertStatus(200)
    ->assertJson([
        'status' => 'OK'
    ]);

    $this->assertModelMissing($users[1]);
});

test('a user with no permissions cannot delete a user account', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $this->deleteJson("/api/admin/users/{$users[1]->id}")
    ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $users[1]->id]);
});

test('an admin can ban a user account', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $user->assignRole('Super Admin');

    $this->patchJson("/api/admin/users/{$users[1]->id}/toggle")
    ->assertStatus(200)
    ->assertJson([
        'active' => false,
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $users[1]->id,
        'active' => false,
    ]);
});

test('a user with no permissions cannot ban a user account', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $this->patchJson("/api/admin/users/{$users[1]->id}/toggle")
    ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $users[1]->id,
        'active' => true,
    ]);
});

test('an admin can unban a user account', function () {
    $users = User::factory()->times(2)->state(new Sequence(
        ['active' => true],
        ['active' => false],
    ))->create();

    Sanctum::actingAs($user = $users[0]);

    $user->assignRole('Super Admin');

    $this->patchJson("/api/admin/users/{$users[1]->id}/toggle")
    ->assertStatus(200)
    ->assertJson([
        'active' => true,
    ]);

    $this->assertDatabaseHas('users', [
        'id' => $users[1]->id,
        'active' => true,
    ]);
});

test('a user with no permissions cannot unban a user account', function () {
    $users = User::factory()->times(2)->state(new Sequence(
        ['active' => true],
        ['active' => false],
    ))->create();

    Sanctum::actingAs($user = $users[0]);

    $this->patchJson("/api/admin/users/{$users[1]->id}/toggle")
    ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $users[1]->id,
        'active' => false,
    ]);
});

test('super admin cannot be updated', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $user->givePermissionTo('users.update');
    $users[1]->assignRole('Super Admin');

    $role = Role::create(['name' => 'Test', 'guard_name' => 'web']);

    $this->putJson("/api/admin/users/{$users[1]->id}", [
        'name' => 'New name',
        'email' => 'new-email@example.com',
        'role_id' => $role->id,
    ])
    ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $users[1]->id,
        'name' => $users[1]->name,
        'email' => $users[1]->email,
        'active' => $users[1]->active,
    ]);

    expect($users[1]->hasRole('Super Admin'))->toBeTrue();
});

test('super admin cannot be deleted', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $user->givePermissionTo('users.delete');
    $users[1]->assignRole('Super Admin');

    $this->deleteJson("/api/admin/users/{$users[1]->id}")
    ->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $users[1]->id]);
});

test('super admin cannot be banned', function () {
    $users = User::factory()->times(2)->create();

    Sanctum::actingAs($user = $users[0]);

    $user->givePermissionTo('users.toggle');
    $users[1]->assignRole('Super Admin');

    $this->patchJson("/api/admin/users/{$users[1]->id}/toggle")
    ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $users[1]->id,
        'active' => true,
    ]);
});
