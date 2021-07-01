<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\UserAccountGenerated;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Sequence;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_list_can_be_retrieved()
    {
        $this->actingAs($user = User::factory()->create());

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
    public function user_can_be_stored()
    {
        $this->actingAs($user = User::factory()->create());

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

        $this->postJson('/api/admin/users', [
            'name' => 'Test',
            'email' => $user->email,
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['email']
        ]);
    }

    /** @test */
    public function user_can_be_updated()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

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
    public function user_can_be_deleted()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

        $this->deleteJson("/api/admin/users/{$users[1]->id}")
        ->assertStatus(200)
        ->assertJson([
            'status' => 'OK'
        ]);

        $this->assertDeleted($users[1]);
    }

    /** @test */
    public function user_account_can_be_banned()
    {
        $users = User::factory()->times(2)->create();

        $this->actingAs($user = $users[0]);

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
    public function user_account_can_be_unbanned()
    {
        $users = User::factory()->times(2)->state(new Sequence(
            ['active' => true],
            ['active' => false],
        ))->create();

        $this->actingAs($user = $users[0]);

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
}
