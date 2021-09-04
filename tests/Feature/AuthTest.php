<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Laravel\Fortify\Features;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /** @test */
    public function can_register()
    {
        $attrs = ['id', 'name', 'email', 'photo_url'];

        if (Features::enabled(Features::emailVerification())) {
            $attrs[] = 'email_verified_at';
        }

        $this->postJson('/register', [
            'name'     => 'Laravel Nuxt',
            'email'    => 'admin@test.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertCreated()
        ->assertJsonStructure($attrs);
    }

    /** @test */
    public function can_login()
    {
        $attrs = ['id', 'name', 'email', 'photo_url'];

        if (Features::enabled(Features::emailVerification())) {
            $attrs[] = 'email_verified_at';
        }

        $user = User::factory()->create();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password'
        ])
        ->assertStatus(200)
        ->assertJsonStructure($attrs);

        $this->assertAuthenticated('sanctum');
    }

    /** @test */
    public function cannot_login_if_account_is_banned()
    {
        $user = User::factory()->banned()->create();

        $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password'
        ])
        ->assertForbidden()
        ->assertJson([
            'status' => __('Your account is currently banned!'),
        ]);

        $this->assertGuest('sanctum');
    }

    /** @test */
    public function can_logout()
    {
        $this->actingAs($user = User::factory()->create());

        $this->postJson('/logout')->assertStatus(204);
        $this->assertGuest('sanctum');
    }

    /** @test */
    public function can_retrive_current_user()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $attrs = [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'photo_url' => env('APP_URL') . '/storage/default-avatar.png',
        ];

        if (Features::enabled(Features::emailVerification())) {
            $attrs['email_verified_at'] = optional($user->email_verified_at)->toJson();
        }

        $this->getJson('/api/user')
            ->assertJson($attrs);
    }

    public function user_session_is_invalidated_after_its_account_has_been_banned()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson('/api/user')->assertStatus(200);

        $user->update(['active' => false]);

        $this->getJson('/api/user')
        ->assertForbidden()
        ->assertJson([
            'status' => __('Your account is currently banned!'),
        ]);

        $this->assertGuest('sanctum');
    }
}
