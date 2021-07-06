<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\UserAccountGenerated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function an_admin_can_fetch_users_list()
    {
        $this->actingAs($user = User::factory()->create());

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
    public function a_user_with_no_permissions_cannot_get_the_users_list()
    {
        $this->actingAs($user = User::factory()->create());

        $this->getJson('/api/admin/users')
        ->assertForbidden();
    }

    /** @test */
    public function an_admin_can_create_a_user_account()
    {
        $this->actingAs($user = User::factory()->create());

        $user->assignRole('Super Admin');

        Notification::fake();

        $response = $this->postJson('/api/admin/users', [
            'name' => 'Test',
            'email' => 'test@example.com',
        ])
        ->assertCreated()
        ->assertJsonStructure(['id', 'name', 'email', 'active']);

        Notification::assertSentTo(
            User::firstWhere('id', $response->json()['id']),
            UserAccountGenerated::class
        );

        $this->assertDatabaseHas('users', $response->json());
    }

    /** @test */
    public function user_cannot_be_stored_with_duplicated_email()
    {
        $this->actingAs($user = User::factory()->create());

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
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_create_a_user_account()
    {
        $this->actingAs($user = User::factory()->create());

        Notification::fake();

        $response = $this->postJson('/api/admin/users', $form = [
            'name' => 'Test',
            'email' => 'test@example.com',
        ])
        ->assertForbidden();

        Notification::assertNothingSent();

        $this->assertDatabaseMissing('users', $form);
    }

    /** @test */
    public function an_admin_can_update_a_user_account()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

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

        $this->assertDatabaseHas('users', $response->json());
    }

    /** @test */
    public function user_cannot_be_updated_with_duplicated_email()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

        $this->putJson("/api/admin/users/{$users[1]->id}", [
            'name' => 'New name',
            'email' => $user->email,
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['email']
        ]);
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_update_a_user_account()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

        $this->putJson("/api/admin/users/{$users[1]->id}", $form = [
            'name' => 'New name',
            'email' => 'new-email@example.com',
        ])
        ->assertForbidden();

        $this->assertDatabaseHas('users', $users[1]->toArray());
    }

    /** @test */
    public function an_admin_can_deleted_a_user_account()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

        $user->assignRole('Super Admin');

        $this->deleteJson("/api/admin/users/{$users[1]->id}")
        ->assertStatus(200)
        ->assertJson([
            'status' => 'OK'
        ]);

        $this->assertDeleted($users[1]);
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_delete_a_user_account()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

        $this->deleteJson("/api/admin/users/{$users[1]->id}")
        ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $users[1]->id]);
    }

    /** @test */
    public function an_admin_can_ban_a_user_account()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

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
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_ban_a_user_account()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

        $this->patchJson("/api/admin/users/{$users[1]->id}/toggle")
        ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $users[1]->id,
            'active' => true,
        ]);
    }

    /** @test */
    public function an_admin_can_unban_a_user_account()
    {
        $users = User::factory()->times(2)->state(new Sequence(
            ['active' => true],
            ['active' => false],
        ))->create();

        $this->actingAs($user = $users[0]);

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
    }

    /** @test */
    public function a_user_with_no_permissions_cannot_unban_a_user_account()
    {
        $users = User::factory()->times(2)->state(new Sequence(
            ['active' => true],
            ['active' => false],
        ))->create();

        $this->actingAs($user = $users[0]);

        $this->patchJson("/api/admin/users/{$users[1]->id}/toggle")
        ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $users[1]->id,
            'active' => false,
        ]);
    }
}
