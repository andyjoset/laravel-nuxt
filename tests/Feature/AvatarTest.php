<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;

test('user avatar can be updated', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $avatar = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->putJson('/api/user/avatar', [
        'avatar' => $avatar
    ])
    ->assertStatus(200)
    ->assertJsonStructure(['photo_url']);

    Storage::assertExists($path = 'avatars/' . $avatar->hashName());

    // Ensure default is not being deleted
    Storage::assertExists(User::DEFAULT_AVATAR_PATH);

    expect($user->fresh()->avatar)->toEqual($path);
    expect($user->fresh()->photo_url)->toEqual($response->json()['photo_url']);
});

test('user avatar cannot be updated if file is not an image', function () {
    Sanctum::actingAs($user = User::factory()->create());

    $this->putJson('/api/user/avatar', [
        'avatar' => UploadedFile::fake()->image('wrong-avatar.pdf')
    ])
    ->assertStatus(422)
    ->assertJsonStructure([
        'message',
        'errors' => ['avatar']
    ]);
});

test('user avatar can be restored to defaults', function () {
    Sanctum::actingAs($user = User::factory()->create([
        'avatar' => $avatar = UploadedFile::fake()->image('avatar.jpg')->store('avatars')
    ]));

    $response = $this->deleteJson('/api/user/avatar')
                    ->assertStatus(200)
                    ->assertJsonStructure(['photo_url']);

    Storage::assertMissing($avatar);
    Storage::assertExists(User::DEFAULT_AVATAR_PATH);

    expect($user->fresh()->avatar)->toEqual(User::DEFAULT_AVATAR_PATH);
    expect($user->fresh()->photo_url)->toEqual($response->json()['photo_url']);
});
