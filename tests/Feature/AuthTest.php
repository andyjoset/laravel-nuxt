<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    /** @test */
    public function can_register()
    {
        $this->postJson('/register', [
            'name'     => 'Laravel Nuxt',
            'email'    => 'admin@test.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertCreated()
        ->assertJsonStructure(['id', 'name', 'email', 'photo_url']);
    }

    /** @test */
    public function can_login()
    {
        $this->postJson('/login', [
            'email' => 'test@test.test',
            'password' => 'password'
        ])
        ->assertStatus(200)
        ->assertJsonStructure(['id', 'name', 'email', 'photo_url']);

        $this->assertAuthenticated('sanctum');
    }

    /** @test */
    public function can_logout()
    {
        $this->actingAs($this->user);

        $this->postJson('/logout');
        $this->getJson('/api/user')->assertStatus(401);
    }

    /** @test */
    public function can_retrive_current_user()
    {
        Sanctum::actingAs($this->user);

        $this->getJson('/api/user')
            ->assertJson([
                'id'        => $this->user->id,
                'name'      => $this->user->name,
                'email'     => $this->user->email,
                'photo_url' => env('APP_URL') . '/storage/default-avatar.png',
            ]);
    }

    protected function createUser()
    {
        return User::factory()->create(['email' => 'test@test.test']);
    }
}
