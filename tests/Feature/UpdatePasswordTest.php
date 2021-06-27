<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function password_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        $response = $this->putJson('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    /** @test */
    public function current_password_must_be_correct()
    {
        $this->actingAs($user = User::factory()->create());

        $this->putJson('/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['current_password']
        ]);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    /** @test */
    public function new_passwords_must_match()
    {
        $this->actingAs($user = User::factory()->create());

        $this->putJson('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'wrong-password',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['password']
        ]);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
