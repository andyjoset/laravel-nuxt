<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Laravel\Fortify\Features;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->user = createUser();
});

test('can register from api', function () {
    $this->postJson('/api/register', [
        'name'     => 'Laravel Nuxt',
        'email'    => 'user@test.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
    ->assertCreated()
    ->assertJsonStructure(['token', 'expires']);
});

test('can login from api', function () {
    $response = $this->postJson('/api/login/token', [
        'email' => 'test@test.test',
        'password' => 'password'
    ])
    ->assertStatus(200)
    ->assertJsonStructure(['token', 'expires']);

    $token = PersonalAccessToken::findToken($response->json()['token']);
    expect($token->isLongLived)->toBeFalse();
});

function can_login_from_api_using_remember_me()
{
    $response = $this->postJson('/api/login/token', [
        'email' => 'test@test.test',
        'password' => 'password',
        'remember' => true,
    ])
    ->assertStatus(200)
    ->assertJsonStructure(['token', 'expires']);

    $token = PersonalAccessToken::findToken($response->json()['token']);
    $cacheKey = "tokens.long-lived.{$token->getKey()}";

    expect(Cache::has($cacheKey))->toBeTrue();

    $cachedToken = Cache::get($cacheKey);
    expect($token->getKey())->toEqual($cachedToken->getKey());
    expect($token->name)->toEqual($cachedToken->name);
}

test('cannot login from api if account is banned', function () {
    $user = User::factory()->create(['active' => false]);

    $response = $this->postJson('/api/login/token', [
        'email' => $user->email,
        'password' => 'password'
    ])
    ->assertForbidden()
    ->assertJson([
        'status' => __('Your account is currently banned!'),
    ]);
});

test('can logout from api', function () {
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
});

test('can retrive current user using token', function () {
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
});

function createUser()
{
    return User::factory()->create(['email' => 'test@test.test']);
}
