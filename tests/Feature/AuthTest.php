<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Laravel\Fortify\Features;

test('can register', function () {
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
});

test('can login', function () {
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
});

test('cannot login if account is banned', function () {
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
});

test('can logout', function () {
    $this->actingAs($user = User::factory()->create());

    $this->postJson('/logout')->assertStatus(204);
    $this->assertGuest('sanctum');
});

test('can retrive current user', function () {
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
});

function user_session_is_invalidated_after_its_account_has_been_banned()
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
