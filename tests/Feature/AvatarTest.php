<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_avatar_can_be_updated()
    {
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

        $this->assertEquals($path, $user->fresh()->avatar);
        $this->assertEquals($response->json()['photo_url'], $user->fresh()->photo_url);
    }

    public function test_user_avatar_cannot_be_updated_if_file_is_not_an_image()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->putJson('/api/user/avatar', [
            'avatar' => UploadedFile::fake()->image('wrong-avatar.pdf')
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => ['avatar']
        ]);
    }

    public function test_user_avatar_can_be_restored_to_defaults()
    {
        Sanctum::actingAs($user = User::factory()->create([
            'avatar' => $avatar = UploadedFile::fake()->image('avatar.jpg')->store('avatars')
        ]));

        $response = $this->deleteJson('/api/user/avatar')
                        ->assertStatus(200)
                        ->assertJsonStructure(['photo_url']);

        Storage::assertMissing($avatar);
        Storage::assertExists(User::DEFAULT_AVATAR_PATH);

        $this->assertEquals(User::DEFAULT_AVATAR_PATH, $user->fresh()->avatar);
        $this->assertEquals($response->json()['photo_url'], $user->fresh()->photo_url);
    }
}
