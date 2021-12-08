<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_confirmed_password_status()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/user/confirmed-password-status');

        $response
            ->assertOk()
            ->assertJsonStructure(['confirmed']);
    }

    /** @test */
    public function password_can_be_confirmed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/user/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertStatus(201);

        $this->actingAs($user)
            ->getJson('/user/confirmed-password-status')
            ->assertOk()
            ->assertJson(['confirmed' => true]);
    }

    /** @test */
    public function password_is_not_confirmed_with_invalid_password()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/user/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password']
            ]);

        $this->actingAs($user)
            ->getJson('/user/confirmed-password-status')
            ->assertOk()
            ->assertJson(['confirmed' => false]);
    }

    /** @test */
    public function check_confirmed_password_status_from_api()
    {
        $token = User::factory()->create()->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/user/confirmed-password-status');

        $response
            ->assertOk()
            ->assertJsonStructure(['confirmed']);
    }

    /** @test */
    public function password_can_be_confirmed_from_api()
    {
        $token = User::factory()->create()->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/user/confirm-password', [
                'password' => 'password',
            ]);

        $response->assertStatus(201);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/user/confirmed-password-status')
            ->assertOk()
            ->assertJson(['confirmed' => true]);
    }

    /** @test */
    public function password_is_not_confirmed_with_invalid_password_from_api()
    {
        $token = User::factory()->create()->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/user/confirm-password', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password']
            ]);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/user/confirmed-password-status')
            ->assertOk()
            ->assertJson(['confirmed' => false]);
    }
}
