<?php

use App\Models\User;

test('check confirmed password status', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/user/confirmed-password-status');

    $response
        ->assertOk()
        ->assertJsonStructure(['confirmed']);
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/user/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertStatus(201);

    $this->actingAs($user)
        ->getJson('/user/confirmed-password-status')
        ->assertOk()
        ->assertJson(['confirmed' => true]);
});

test('password is not confirmed with invalid password', function () {
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
});

test('check confirmed password status from api', function () {
    $token = User::factory()->create()->createToken('Test Token')->plainTextToken;

    $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
        ->getJson('/api/user/confirmed-password-status');

    $response
        ->assertOk()
        ->assertJsonStructure(['confirmed']);
});

test('password can be confirmed from api', function () {
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
});

test('password is not confirmed with invalid password from api', function () {
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
});
