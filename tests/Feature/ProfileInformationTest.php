<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile information can be updated', function () {
    $this->actingAs($user = User::factory()->create());

    $response = $this->putJson('/user/profile-information', [
        'name'   => 'Test Name',
        'email'  => 'test@example.com',
    ]);

    $user->fresh();

    expect($user->name)->toEqual('Test Name');
    expect($user->email)->toEqual('test@example.com');
});

test('profile information cannot be updated with duplicated email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $this->actingAs($user = User::factory()->create());

    $this->putJson('/user/profile-information', [
        'name'   => 'Test Name',
        'email'  => 'test@example.com',
    ])
    ->assertStatus(422)
    ->assertJsonStructure([
        'message',
        'errors' => ['email']
    ]);
});
