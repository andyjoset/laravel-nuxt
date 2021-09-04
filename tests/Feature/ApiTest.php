<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Laravel\Fortify\Features;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
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
    public function can_register_from_api()
    {
        $this->postJson('/api/register', [
            'name'     => 'Laravel Nuxt',
            'email'    => 'user@test.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertCreated()
        ->assertJsonStructure(['token']);
    }

    /** @test */
    public function can_login_from_api()
    {
        $response = $this->postJson('/api/login/token', [
            'email' => 'test@test.test',
            'password' => 'password'
        ])
        ->assertStatus(200)
        ->assertJsonStructure(['token']);
    }

    /** @test */
    public function cannot_login_from_api_if_account_is_banned()
    {
        $user = User::factory()->create(['active' => false]);

        $response = $this->postJson('/api/login/token', [
            'email' => $user->email,
            'password' => 'password'
        ])
        ->assertForbidden()
        ->assertJson([
            'status' => __('Your account is currently banned!'),
        ]);
    }

    /** @test */
    public function can_logout_from_api()
    {
        $token = $this->postJson('/api/login/token', [
            'email' => $this->user->email,
            'password' => 'password',
        ])->json()['token'];

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/logout/token')
            ->assertStatus(200)
            ->assertJson(['status' => 'OK']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'name'         => 'API Token',
            'tokenable_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function can_retrive_current_user_using_token()
    {
        $response = $this->postJson('/api/login/token', [
            'email' => 'test@test.test',
            'password' => 'password'
        ]);

        $attrs = [
            'id'        => $this->user->id,
            'name'      => $this->user->name,
            'email'     => $this->user->email,
            'photo_url' => env('APP_URL') . '/storage/default-avatar.png',
        ];

        if (Features::enabled(Features::emailVerification())) {
            $attrs['email_verified_at'] = optional($this->user->email_verified_at)->toJson();
        }

        $this->withHeaders(['Authorization' => "Bearer {$response->json()['token']}"])
            ->getJson('/api/user')
            ->assertStatus(200)
            ->assertJson($attrs);
    }

    protected function createUser()
    {
        return User::factory()->create(['email' => 'test@test.test']);
    }
}
